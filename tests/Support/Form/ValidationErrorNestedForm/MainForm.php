<?php

declare(strict_types=1);

namespace Yiisoft\FormModel\Tests\Support\Form\ValidationErrorNestedForm;

use Yiisoft\FormModel\FormModel;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Rule\StringValue;


class MainForm extends FormModel {

    #[Nested(FirstNestedForm::class)]
    public ?FirstNestedForm $firstForm = null;

    #[Required]
    #[StringValue]
    #[Length(min: 4)]
    public string $value = '';

    public function __construct() {
        $this->firstForm = new FirstNestedForm();
    }
}
