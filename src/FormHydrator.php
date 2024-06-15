<?php

declare(strict_types=1);

namespace Yiisoft\FormModel;

use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Hydrator\ArrayData;
use Yiisoft\Hydrator\HydratorInterface;
use Yiisoft\Validator\Helper\ObjectParser;
use Yiisoft\Validator\RulesProviderInterface;
use Yiisoft\Validator\ValidatorInterface;

use function is_array;

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
     * If not provided, it is generated automatically based on presence of property validation rules.
     * @psalm-param MapType $map
     * @param ?bool $strict TODO: document better!
     * @param ?string $scope Hydration scope. TODO: more!
     *
     * By fact hydration parameters (`map` and `strict`) are based on passed parameters `map` and `strict`:
     *
     * - strict is null, user map is null — generated map, strict
     * - strict is true, user map is null — generated map, strict
     * - strict is false, user map is null — without map, not strict
     * - strict is null, user map is array — user map + generated map, strict
     * - strict is true, user map is array — user map, strict
     * - strict is false, user map is array — user map, not strict
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
     * Fill the model with the data and validate it.
     *
     * @param FormModelInterface $model Model to fill.
     * @param mixed $data Data to fill model with.
     * @psalm-param MapType $map
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

        return $this->validator->validate($model)->isValid();
    }

    /**
     * @psalm-param MapType $map
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
     * @psalm-param MapType $map
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
     * @psalm-param MapType $userMap
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
     * @psalm-return list<string>
     */
    private function getPropertiesWithRules(FormModelInterface $model): array
    {
        if ($model instanceof RulesProviderInterface) {
            return $this->extractStringKeys($model->getRules());
        }

        $parser = new ObjectParser($model, skipStaticProperties: true);
        return $this->extractStringKeys($parser->getRules());
    }

    /**
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
