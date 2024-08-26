<?php

declare(strict_types=1);

namespace Yiisoft\FormModel\Tests\Support\Form;

use Yiisoft\FormModel\Attribute\Hint;
use Yiisoft\FormModel\Attribute\Placeholder;
use Yiisoft\FormModel\FormModel;
use Yiisoft\Validator\Label;

final class AttributeForm extends FormModel
{
    #[Label('AgeLabelFromAttribute')]
    #[Hint('AgeHintFromAttribute')]
    #[Placeholder('AgePlaceholderFromAttribute')]
    public string $age;

    #[Label('NameLabelFromAttribute')]
    #[Hint('NameHintFromAttribute')]
    #[Placeholder('NamePlaceholderFromAttribute')]
    public string $name;

    public function getPropertyLabels(): array
    {
        return [
            'name' => 'NameLabelFromGetter',
        ];
    }

    public function getPropertyHints(): array
    {
        return [
            'name' => 'NameHintFromGetter',
        ];
    }

    public function getPropertyPlaceholders(): array
    {
        return [
            'name' => 'NamePlaceholderFromGetter',
        ];
    }
}
