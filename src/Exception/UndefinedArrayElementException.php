<?php

declare(strict_types=1);

namespace Yiisoft\FormModel\Exception;

/**
 * Thrown when an array element is undefined.
 */
final class UndefinedArrayElementException extends ValueNotFoundException
{
    /**
     * @param string $property Name of the property.
     */
    public function __construct(string $property)
    {
        parent::__construct('Undefined array element: "' . $property . '".');
    }
}
