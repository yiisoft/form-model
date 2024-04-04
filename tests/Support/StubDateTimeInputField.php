<?php

declare(strict_types=1);

namespace Yiisoft\FormModel\Tests\Support;

use DateTimeInterface;
use Yiisoft\Form\Field\Base\DateTimeInputField;

final class StubDateTimeInputField extends DateTimeInputField
{
    protected function getInputType(): string
    {
        return 'datetime';
    }

    protected function formatDateTime(DateTimeInterface $value): string
    {
        return $value->format('Y-m-d\TH:i');
    }
}
