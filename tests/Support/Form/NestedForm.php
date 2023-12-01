<?php

declare(strict_types=1);

namespace Yiisoft\FormModel\Tests\Support\Form;

use Yiisoft\FormModel\FormModel;

final class NestedForm extends FormModel
{
    public array $letters = ['A'];
    public object $object;

    public function __construct()
    {
        $this->object = new class () {
            public string $name = 'Bo';
            public array $numbers = [7, 42];
        };
    }
}
