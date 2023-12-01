<?php

declare(strict_types=1);

namespace Yiisoft\FormModel\Exception;

final class PropertyNotSupportNestedValuesException extends ValueNotFoundException
{
    public function __construct(
        string $property,
        private readonly mixed $value,
    ) {
        parent::__construct('Property "' . $property . '" not support nested values.');
    }

    public function getValue(): mixed
    {
        return $this->value;
    }
}
