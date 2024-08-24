<?php

declare(strict_types=1);

namespace Yiisoft\FormModel\Tests\Support\Form;

use Yiisoft\FormModel\FormModel;
use Yiisoft\Validator\Label;

final class LabelForm extends FormModel
{
    #[Label('AgeFromAttribute')]
    public string $age;

    #[Label('NameFromAttribute')]
    public string $name;

    public function getPropertyLabels(): array
    {
        return [
            'name' => 'NameFromGetter'
        ];
    }
}
