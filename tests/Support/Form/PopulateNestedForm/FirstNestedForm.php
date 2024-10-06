<?php

declare(strict_types=1);

namespace Yiisoft\FormModel\Tests\Support\Form\PopulateNestedForm;

use Yiisoft\FormModel\FormModel;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Rule\StringValue;

class FirstNestedForm extends FormModel
{
    public static string $static = '';

    #[Required]
    #[StringValue]
    #[Length(min: 3)]
    public string $value = '';

    #[Required]
    #[Nested(SecondNestedForm::class)]
    private SecondNestedForm $secondForm;

    public readonly string $readonly;

    public function __construct()
    {
        $this->secondForm = new SecondNestedForm();
    }

    public function secondForm(): SecondNestedForm
    {
        return $this->secondForm;
    }

}
