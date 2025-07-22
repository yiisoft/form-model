<?php

declare(strict_types=1);

namespace Yiisoft\FormModel;

use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Hydrator\ArrayData;
use Yiisoft\Hydrator\HydratorInterface;
use Yiisoft\Hydrator\ObjectMap;
use Yiisoft\Validator\Helper\ObjectParser;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\RulesProviderInterface;
use Yiisoft\Validator\ValidatorInterface;

use function array_merge;
use function is_array;

/**
 * Form hydrator fills model with the data and optionally checks the data validity.
 *
 * @psalm-import-type MapType from ArrayData
 * @psalm-import-type RawRulesMap from ValidatorInterface
 * @psalm-import-type NormalizedNestedRulesArray from Nested
 */
final class FormHydrator
{
    /**
     * @param HydratorInterface $hydrator Hydrator to use to fill model with data.
     * @param ValidatorInterface $validator Validator to use to check data before filling a model.
     */
    public function __construct(
        private readonly HydratorInterface $hydrator,
        private readonly ValidatorInterface $validator,
    ) {
    }

    /**
     * Fill the model with the data.
     *
     * @param FormModelInterface $model Model to fill.
     * @param mixed $data Data to fill model with.
     * @param ?array $map Map of object property names to keys in the data array to use for hydration.
     * If not provided, it may be generated automatically based on presence of property validation rules and a `strict`
     * setting.
     * @psalm-param MapType $map
     * @param ?bool $strict Whether to enable strict mode for filling data:
     * - If `false`, fills everything that is in the data.
     * = If `null`, fills data that is either defined in a map explicitly or allowed via validation rules.
     * - If `true`, fills either only data defined explicitly in a map or only data allowed via validation rules but not
     * both.
     * @param ?string $scope Key to use in the data array as a source of data. Usually used when there are multiple
     * forms at the same page. If not set, it equals to {@see FormModelInterface::getFormName()}.
     */
    public function populate(
        FormModelInterface $model,
        mixed $data,
        ?array $map = null,
        ?bool $strict = null,
        ?string $scope = null
    ): bool {
        if (!is_array($data)) {
            return false;
        }

        $scope ??= $model->getFormName();
        if ($scope === '') {
            $hydrateData = $data;
        } else {
            if (!isset($data[$scope]) || !is_array($data[$scope])) {
                return false;
            }

            $filteredData = $this->filterDataNestedForms($model, $data);
            $hydrateData = array_merge_recursive((array)$data[$model->getFormName()], $filteredData);
        }

        $this->hydrator->hydrate(
            $model,
            new ArrayData(
                $hydrateData,
                $this->createMap($model, $map, $strict),
                $strict ?? true
            )
        );

        return true;
    }

    /**
     * Validate form model.
     *
     * @param FormModelInterface $model Form model to validate.
     *
     * @return Result Validation result.
     */
    public function validate(FormModelInterface $model): Result
    {
        return $this->validator->validate($model);
    }

    /**
     * Fill the model with the data and validate it.
     *
     * @param FormModelInterface $model Model to fill.
     * @param mixed $data Data to fill model with.
     * @param ?array $map Map of object property names to keys in the data array to use for hydration.
     * If not provided, it may be generated automatically based on presence of property validation rules and a `strict`
     * setting.
     * @psalm-param MapType $map
     * @param ?bool $strict If `false`, fills everything that is in the data. If `null`, fills data that is either
     * defined in a map explicitly or allowed via validation rules. If `false`, fills only data defined explicitly
     * in a map or only data allowed via validation rules but not both.
     * @param ?string $scope Key to use in the data array as a source of data. Usually used when there are multiple
     * forms at the same page. If not set, it equals to {@see FormModelInterface::getFormName()}.
     *
     * @return bool Whether model is filled with data and is valid.
     */
    public function populateAndValidate(
        FormModelInterface $model,
        mixed $data,
        ?array $map = null,
        ?bool $strict = null,
        ?string $scope = null
    ): bool {
        if (!$this->populate($model, $data, $map, $strict, $scope)) {
            return false;
        }

        return $this->validate($model)->isValid();
    }

    /**
     * Fill the model with the data parsed from request body.
     *
     * @param FormModelInterface $model Model to fill.
     * @param ServerRequestInterface $request Request to get parsed data from.
     * @param ?array $map Map of object property names to keys in the data array to use for hydration.
     * If not provided, it may be generated automatically based on presence of property validation rules and a `strict`
     * setting.
     * @psalm-param MapType $map
     * @param ?bool $strict If `false`, fills everything that is in the data. If `null`, fills data that is either
     * defined in a map explicitly or allowed via validation rules. If `false`, fills only data defined explicitly
     * in a map or only data allowed via validation rules but not both.
     * @param ?string $scope Key to use in the data array as a source of data. Usually used when there are multiple
     * forms at the same page. If not set, it equals to {@see FormModelInterface::getFormName()}.
     */
    public function populateFromPost(
        FormModelInterface $model,
        ServerRequestInterface $request,
        ?array $map = null,
        ?bool $strict = null,
        ?string $scope = null
    ): bool {
        if ($request->getMethod() !== 'POST') {
            return false;
        }

        return $this->populate($model, $request->getParsedBody(), $map, $strict, $scope);
    }

    /**
     * Fill the model with the data parsed from request body and validate it.
     *
     * @param FormModelInterface $model Model to fill.
     * @param ServerRequestInterface $request Request to get parsed data from.
     * @param ?array $map Map of object property names to keys in the data array to use for hydration.
     * If not provided, it may be generated automatically based on presence of property validation rules and a `strict`
     * setting.
     * @psalm-param MapType $map
     * @param ?bool $strict If `false`, fills everything that is in the data. If `null`, fills data that is either
     * defined in a map explicitly or allowed via validation rules. If `false`, fills only data defined explicitly
     * in a map or only data allowed via validation rules but not both.
     * @param ?string $scope Key to use in the data array as a source of data. Usually used when there are multiple
     * forms at the same page. If not set, it equals to {@see FormModelInterface::getFormName()}.
     *
     * @return bool Whether model is filled with data and is valid.
     */
    public function populateFromPostAndValidate(
        FormModelInterface $model,
        ServerRequestInterface $request,
        ?array $map = null,
        ?bool $strict = null,
        ?string $scope = null
    ): bool {
        if ($request->getMethod() !== 'POST') {
            return false;
        }

        return $this->populateAndValidate($model, $request->getParsedBody(), $map, $strict, $scope);
    }

    private function filterDataNestedForms(FormModelInterface $formModel, array &$data): array
    {
        $reflection = new \ReflectionClass($formModel);
        $properties = $reflection->getProperties(
            \ReflectionProperty::IS_PUBLIC |
            \ReflectionProperty::IS_PROTECTED |
            \ReflectionProperty::IS_PRIVATE,
        );

        $filteredData = [];
        foreach ($properties as $property) {
            if ($property->isStatic()) {
                continue;
            }

            if ($property->isReadOnly()) {
                continue;
            }

            $propertyValue = $property->getValue($formModel);
            if ($propertyValue instanceof FormModelInterface) {
                $dataNestedForms = $this->filterDataNestedForms($propertyValue, $data);
                if (isset($data[$propertyValue->getFormName()])) {
                    $filteredData[$property->getName()] = array_merge(
                        (array)$data[$propertyValue->getFormName()],
                        $dataNestedForms,
                    );
                    unset($data[$propertyValue->getFormName()]);
                } elseif (!empty($dataNestedForms)) {
                    $filteredData[$property->getName()] = $dataNestedForms;
                }
            }
        }

        return $filteredData;
    }

    /**
     * Get a map of object property names mapped to keys in the data array.
     *
     * @param FormModelInterface $model Model to read validation rules from.
     * @param ?array $userMap Explicit map defined by user.
     * @psalm-param MapType $userMap
     * @param ?bool $strict If `false`, fills everything that is in the data. If `null`, fills data that is either
     * defined in a map explicitly or allowed via validation rules. If `false`, fills only data defined explicitly
     * in a map or only data allowed via validation rules but not both.
     *
     * @return array A map of object property names mapped to keys in the data array.
     * @psalm-return MapType
     */
    private function createMap(FormModelInterface $model, ?array $userMap, ?bool $strict): array
    {
        if ($strict === false) {
            return $userMap ?? [];
        }

        if ($strict && $userMap !== null) {
            return $userMap;
        }

        $map = $this->getMapFromRules($model);

        if ($userMap === null) {
            return $map;
        }

        return $this->mapMerge($userMap, $map);
    }

    /**
     * Extract object property names mapped to keys in the data array based on model validation rules.
     *
     * @return array Object property names mapped to keys in the data array.
     * @psalm-return MapType
     */
    private function getMapFromRules(FormModelInterface $model): array
    {
        $parser = new ObjectParser($model, skipStaticProperties: true);
        $mapFromAttributes = $this->getMapFromRulesAttributes($parser->getRules());

        if ($model instanceof RulesProviderInterface) {
            $mapFromProvider = $this->getMapFromRulesProvider($model);
            return $this->mapMerge($mapFromAttributes, $mapFromProvider);
        }

        return $mapFromAttributes;
    }

    /**
     * @psalm-return MapType
     */
    private function getMapFromRulesAttributes(array $array): array
    {
        $result = [];
        foreach ($array as $key => $_value) {
            if (is_int($key)) {
                continue;
            }
            $result[$key] = $key;
            foreach ($_value as $nestedRule) {
                if ($nestedRule instanceof Nested) {
                    $nestedMap = $this->getNestedMap($nestedRule, [$key]);
                    if ($nestedMap !== null) {
                        $result[$key] = new ObjectMap($nestedMap);
                    }
                }
            }
        }

        return $result;
    }

    /**
     * @param array<int, string> $parentKeys
     * @psalm-return MapType|null
     */
    private function getNestedMap(Nested $rule, array $parentKeys): ?array
    {
        /**
         * @psalm-param $rules NormalizedNestedRulesArray
         */
        $rules = $rule->getRules();
        if ($rules === null) {
            return null;
        }

        $map = [];
        foreach ($rules as $key => $nestedRules) {
            if (is_int($key)) {
                continue;
            }

            if (is_array($nestedRules)) {
                $keyPath = null;
                if (str_contains($key, '.')) {
                    $keyPath = explode('.', $key);
                    $key = reset($keyPath);
                    $dotKeyMap = $this->dotKeyInMap($keyPath, $parentKeys, null);
                    $map[$key] = $dotKeyMap[$key];
                } else {
                    $map[$key] = [...$parentKeys, $key];
                }
                foreach ($nestedRules as $item) {
                    if ($item instanceof Nested) {
                        $pathKeys = $keyPath ?? [$key];
                        $nestedMap = $this->getNestedMap($item, [...$parentKeys, ...$pathKeys]);
                        if (isset($keyPath)) {
                            $dotKeyMap = $this->dotKeyInMap($keyPath, $parentKeys, $nestedMap);
                            $map[$key] = $dotKeyMap[$key];
                        } elseif ($nestedMap !== null) {
                            $map[$key] = new ObjectMap($nestedMap);
                        }
                    }
                }
            }
        }

        return $map;
    }

    /**
     * @psalm-param array<int, string> $keyPath
     * @psalm-param array<int, string> $parentsKeys
     * @psalm-param MapType|null $nestedMap
     * @psalm-return MapType
     */
    private function dotKeyInMap(array $keyPath, array $parentsKeys, ?array $nestedMap): array
    {
        $dotMap = [];
        $reverseKeyPath = array_reverse($keyPath);
        foreach ($reverseKeyPath as $key) {
            if ($dotMap !== []) {
                $dotMap = [$key => new ObjectMap($dotMap)];
            } else {
                $dotMap = [
                    $key => is_array($nestedMap) ? new ObjectMap($nestedMap) : [...$parentsKeys, ...$keyPath],
                ];
            }
        }

        return $dotMap;
    }

    /**
     * @param array<int, string> $path
     * @psalm-return MapType
     */
    private function getMapFromRulesProvider(
        RulesProviderInterface $formModel,
        array $path = [],
    ): array {
        $mapModel = [];
        /**
         * @psalm-param $rules RawRulesMap
         */
        $rules = $formModel->getRules();
        foreach ($rules as $key => $rule) {
            if (is_int($key)) {
                continue;
            }
            $mapModel[$key] = [...$path, $key];
            if ($rule instanceof Nested) {
                $nestedMap = $this->getNestedMap($rule, [...$path, $key]);
                if ($nestedMap !== null) {
                    $mapModel[$key] = new ObjectMap($nestedMap);
                }
            } elseif (is_array($rule)) {
                foreach ($rule as $ruleKey => $item) {
                    if ($item instanceof Nested) {
                        $nestedMap = $this->getNestedMap($item, [...$path, $key]);
                        if ($nestedMap !== null) {
                            $mapModel[$key] = new ObjectMap($nestedMap);
                        }
                    }
                }
            }
        }

        $mapNestedModels = $this->getMapNestedModels($formModel, $path);

        return $this->mapMerge($mapModel, $mapNestedModels);
    }

    /**
     * @param array<int, string> $path
     * @psalm-return MapType
     */
    private function getMapNestedModels(RulesProviderInterface $formModel, array $path): array
    {
        $reflection = new \ReflectionClass($formModel);
        $properties = $reflection->getProperties(
            \ReflectionProperty::IS_PUBLIC |
            \ReflectionProperty::IS_PROTECTED |
            \ReflectionProperty::IS_PRIVATE,
        );

        $propertiesNestedModels = [];
        foreach ($properties as $property) {
            if ($property->isStatic()) {
                continue;
            }

            if ($property->isReadOnly()) {
                continue;
            }

            $propertyValue = $property->getValue($formModel);
            if ($propertyValue instanceof RulesProviderInterface) {
                $propertiesNestedModels[$property->getName()] = new ObjectMap(
                    $this->getMapFromRulesProvider(
                        $propertyValue,
                        [...$path, $property->getName()],
                    ),
                );
            }
        }

        return $propertiesNestedModels;
    }

    /**
     * @psalm-param MapType $map
     * @psalm-param MapType $secondMap
     * @psalm-return MapType
     */
    private function mapMerge(array $map, array $secondMap): array
    {
        $result = [];
        foreach ($map as $key => $value) {
            if (isset($secondMap[$key]) && $value instanceof ObjectMap && $secondMap[$key] instanceof ObjectMap) {
                $mergedMap = $this->mapMerge($value->map, $secondMap[$key]->map);
                $result[$key] = new ObjectMap($mergedMap);
            } elseif (isset($secondMap[$key]) && $secondMap[$key] instanceof ObjectMap) {
                $result[$key] = $secondMap[$key];
            } else {
                $result[$key] = $value;
            }
        }

        foreach ($secondMap as $key => $value) {
            if (!isset($result[$key])) {
                $result[$key] = $value;
            }
        }

        return $result;
    }
}
