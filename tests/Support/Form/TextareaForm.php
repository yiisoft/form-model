<?php

declare(strict_types=1);

namespace Yiisoft\FormModel\Tests\Support\Form;

use Yiisoft\FormModel\FormModel;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\RulesProviderInterface;

final class TextareaForm extends FormModel implements RulesProviderInterface
{
    private string $desc = '';
    private string $bio = '';
    private string $shortdesc = '';
    private int $age = 42;
    public ?int $requiredWhen = null;

    public function getRules(): array
    {
        return [
            'bio' => [new Required()],
            'shortdesc' => [new Length(min: 10, max: 199)],
            'requiredWhen' => [
                new Required(when: static fn() => false),
                new Length(min: 7)
            ],
        ];
    }

    public function getPropertyLabels(): array
    {
        return [
            'desc' => 'Description',
        ];
    }
}
