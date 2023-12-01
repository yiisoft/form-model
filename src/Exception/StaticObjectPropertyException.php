<?php

declare(strict_types=1);

namespace Yiisoft\FormModel\Exception;

final class StaticObjectPropertyException extends ValueNotFoundException
{
    public function __construct(string $property)
    {
        parent::__construct('Object property is static: "' . $property . '".');
    }
}
