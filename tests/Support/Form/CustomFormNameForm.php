<?php

declare(strict_types=1);

namespace Yiisoft\FormModel\Tests\Support\Form;

use Yiisoft\FormModel\FormModel;

final class CustomFormNameForm extends FormModel
{
    public function getFormName(): string
    {
        return 'my-best-form-name';
    }
}
