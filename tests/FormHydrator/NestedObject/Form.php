<?php

declare(strict_types=1);

namespace Yiisoft\FormModel\Tests\FormHydrator\NestedObject;

use Yiisoft\FormModel\Attribute\Safe;
use Yiisoft\FormModel\FormModel;
use Yiisoft\Validator\Rule\Nested;

final class Form extends FormModel
{
    #[Safe]
    public string $name = '';

    #[Nested]
    public Item $item;

    public function __construct()
    {
        $this->item = new Item();
    }
}
