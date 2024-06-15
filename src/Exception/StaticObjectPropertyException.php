<?php

declare(strict_types=1);

namespace Yiisoft\FormModel\Exception;

/**
 * Thrown when the object property is static.
 */
final class StaticObjectPropertyException extends ValueNotFoundException
{
    public function __construct(string $property)
    {
        parent::__construct('Object property is static: "' . $property . '".');
    }
}
