<?php

declare(strict_types=1);

namespace Yiisoft\FormModel\Tests\Support;

use Yiisoft\Form\Field\Base\DateTimeInputField;

final class StubDateTimeInputField extends DateTimeInputField
{
    protected function getInputType(): string
    {
        return 'datetime';
    }
}
