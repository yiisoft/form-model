<?php

declare(strict_types=1);


namespace Yiisoft\FormModel\Tests\Support\Form\PopulateNestedForm;

use Yiisoft\FormModel\FormModel;
use Yiisoft\Validator\Rule\Integer;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Rule\StringValue;

class SecondNestedForm extends FormModel {

    #[Required]
    #[Integer]
    public int $value = 0;

    #[Required]
    #[StringValue]
    #[Length(min: 4)]
    public string $string = '';

    public function __construct() {}
}
