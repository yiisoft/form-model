<?php

declare(strict_types=1);

namespace Yiisoft\FormModel\Exception;

/**
 * Thrown when object property is undefined.
 */
final class UndefinedObjectPropertyException extends ValueNotFoundException
{
    public function __construct(string $property)
    {
        parent::__construct('Undefined object property: "' . $property . '".');
    }
}
