<?php

declare(strict_types=1);

namespace Yiisoft\FormModel\Tests\Support\Form\PopulateNestedForm;

use Yiisoft\FormModel\FormModel;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Rule\StringValue;

class MainForm extends FormModel
{
    #[Nested(FirstNestedForm::class)]
    protected FirstNestedForm $firstForm;

    #[Required]
    #[StringValue]
    #[Length(min: 3)]
    public string $value = '';

    public function __construct()
    {
        $this->firstForm = new FirstNestedForm();
    }

    public function firstNestedForm(): FirstNestedForm
    {
        return $this->firstForm;
    }

}
