<?php

declare(strict_types=1);

namespace Yiisoft\FormModel\Exception;

/**
 * Thrown when the property doesn't support nested values.
 */
final class PropertyNotSupportNestedValuesException extends ValueNotFoundException
{
    public function __construct(
        string $property,
        private readonly mixed $value,
    ) {
        parent::__construct('Property "' . $property . '" doesn\'t support nested values.');
    }

    public function getValue(): mixed
    {
        return $this->value;
    }
}
