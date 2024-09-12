<?php

declare(strict_types=1);

namespace Yiisoft\FormModel;

use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Hydrator\ArrayData;
use Yiisoft\Hydrator\HydratorInterface;
use Yiisoft\Validator\Helper\ObjectParser;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RulesProviderInterface;
use Yiisoft\Validator\ValidatorInterface;

use function array_merge;
use function is_array;
use function is_string;

/**
 * Form hydrator fills model with the data and optionally checks the data validity.
 *
 * @psalm-import-type MapType from ArrayData
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
            $hydrateData = $data[$scope];
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

        $properties = $this->getPropertiesWithRules($model);
        $generatedMap = array_combine($properties, $properties);

        if ($userMap === null) {
            return $generatedMap;
        }

        return array_merge($generatedMap, $userMap);
    }

    /**
     * Extract object property names mapped to keys in the data array based on model validation rules.
     *
     * @return array Object property names mapped to keys in the data array.
     * @psalm-return array<int, string>
     */
    private function getPropertiesWithRules(FormModelInterface $model): array
    {
        $parser = new ObjectParser($model, skipStaticProperties: true);
        $properties = $this->extractStringKeys($parser->getRules());

        return $model instanceof RulesProviderInterface
            ? array_merge($properties, $this->extractStringKeys($model->getRules()))
            : $properties;
    }

    /**
     * Get only string keys from an array.
     *
     * @return array String keys.
     * @psalm-return list<string>
     */
    private function extractStringKeys(iterable $array): array
    {
        $result = [];
        foreach ($array as $key => $_value) {
            if (is_string($key)) {
                $result[] = $key;
            }
        }
        return $result;
    }
}
