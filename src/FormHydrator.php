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
 * @psalm-import-type MapType from ArrayData
 */
final class FormHydrator
{
    public function __construct(
        private readonly HydratorInterface $hydrator,
        private readonly ValidatorInterface $validator,
    ) {
    }

    /**
     * By fact hydration parameters (`map` and `strict`) based on passed parameters `map` and `strict`:
     *
     * - strict is null, user map is null — generated map, strict
     * - strict is true, user map is null — generated map, strict
     * - strict is false, user map is null — without map, not strict
     * - strict is null, user map is array — user map + generated map, strict
     * - strict is true, user map is array — user map, strict
     * - strict is false, user map is array — user map, not strict
     *
     * User map - map that passed to method.
     * Generated map - map based on presence of property rules.
     *
     * @psalm-param MapType $map
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
     * @psalm-param MapType $map
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
    ): bool
    {
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
