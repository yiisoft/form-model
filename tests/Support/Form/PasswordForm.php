<?php

declare(strict_types=1);

namespace Yiisoft\FormModel\Tests\Support\Form;

use Yiisoft\FormModel\FormModel;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Regex;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\RulesProviderInterface;

final class PasswordForm extends FormModel implements RulesProviderInterface
{
    private string $old = '';
    private ?string $post = null;
    private int $age = 42;
    private ?string $entry1 = null;
    private ?string $entry2 = null;
    private ?string $code = null;
    private ?string $nocode = null;
    public ?int $requiredWhen = null;

    public function getRules(): array
    {
        return [
            'entry1' => [new Required()],
            'entry2' => [new Length(min: 10, max: 199)],
            'code' => [new Regex(pattern: '~\w+~')],
            'nocode' => [new Regex(pattern: '~\w+~', not: true)],
            'requiredWhen' => [
                new Required(when: static fn() => false),
                new Length(min: 7)
            ],
        ];
    }

    public function getPropertyLabels(): array
    {
        return [
            'old' => 'Old password',
        ];
    }

    public function getPropertyHints(): array
    {
        return [
            'old' => 'Enter your old password.',
        ];
    }
}
