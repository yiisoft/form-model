<?php

declare(strict_types=1);

namespace Yiisoft\FormModel\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class Placeholder
{
    public function __construct(
        private string $placeholder,
    ) {
    }

    public function getPlaceholder(): string
    {
        return $this->placeholder;
    }
}
