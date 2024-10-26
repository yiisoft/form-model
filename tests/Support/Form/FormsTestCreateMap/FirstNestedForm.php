<?php

declare(strict_types=1);

namespace Yiisoft\FormModel\Tests\Support\Form\FormsTestCreateMap;

use Yiisoft\FormModel\FormModel;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\Required;

class FirstNestedForm extends FormModel
{
    public function __construct(
        #[Required]
        public string $value = '',
        #[Nested(null)]
        public SecondNestedForm $secondForm = new SecondNestedForm(),
    )
    {
    }
}
