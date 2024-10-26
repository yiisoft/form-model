<?php

declare(strict_types=1);

namespace Yiisoft\FormModel\Tests\Support\Form\FormsTestCreateMap;

use Yiisoft\FormModel\FormModel;

class SecondNestedForm extends FormModel
{
    public function __construct(
        public string $post = '',
        public string $author = '',
    )
    {
    }
}
