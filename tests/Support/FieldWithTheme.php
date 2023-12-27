<?php

declare(strict_types=1);

namespace Yiisoft\FormModel\Tests\Support;

use Yiisoft\FormModel\Field;

final class FieldWithTheme extends Field
{
    protected const DEFAULT_THEME = 'A';
}
