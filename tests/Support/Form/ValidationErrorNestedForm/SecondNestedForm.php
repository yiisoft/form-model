<?php

declare(strict_types=1);


namespace Yiisoft\FormModel\Tests\Support\Form\ValidationErrorNestedForm;

use Yiisoft\FormModel\FormModel;
use Yiisoft\Validator\Rule\Integer;
use Yiisoft\Validator\Rule\Required;

class SecondNestedForm extends FormModel {

    #[Required]
    #[Integer(min: 10, max: 20)]
    public int $number = 0;

    public function __construct() {}
}
