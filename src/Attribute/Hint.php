<?php

declare(strict_types=1);

namespace Yiisoft\FormModel\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class Hint
{
    public function __construct(
        private string $hint,
    ) {
    }

    public function getHint(): string
    {
        return $this->hint;
    }
}
