<?php

declare(strict_types=1);

namespace Yiisoft\FormModel\Tests\Support\Form;

use Yiisoft\FormModel\FormModel;
use Yiisoft\Validator\Rule\Length;

final class CarForm extends FormModel
{
    #[Length(min: 3)]
    public string $name = '';
}
