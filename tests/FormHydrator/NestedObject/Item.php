<?php

declare(strict_types=1);

namespace Yiisoft\FormModel\Tests\FormHydrator\NestedObject;

use Yiisoft\FormModel\Attribute\Safe;
use Yiisoft\FormModel\FormModel;

final class Item extends FormModel
{
    public string $color = '';

    #[Safe]
    public string $size = '';
}
