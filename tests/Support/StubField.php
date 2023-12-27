<?php

declare(strict_types=1);

namespace Yiisoft\FormModel\Tests\Support;

use Yiisoft\Form\Field\Base\BaseField;

final class StubField extends BaseField
{
    protected function generateContent(): ?string
    {
        return '';
    }
}
