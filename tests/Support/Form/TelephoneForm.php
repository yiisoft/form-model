<?php

declare(strict_types=1);

namespace Yiisoft\FormModel\Tests\Support\Form;

use Yiisoft\FormModel\FormModel;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Regex;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\RulesProviderInterface;

final class TelephoneForm extends FormModel implements RulesProviderInterface
{
    private string $number = '';
    private ?string $main = null;
    private ?string $office1 = null;
    private ?string $office2 = null;
    private ?string $code = null;
    private ?string $nocode = null;
    private int $age = 42;
    public ?int $requiredWhen = null;

    public function getRules(): array
    {
        return [
            'office1' => [new Required()],
            'office2' => [new Length(min: 10, max: 199)],
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
            'number' => 'Phone',
        ];
    }

    public function getPropertyHints(): array
    {
        return [
            'number' => 'Enter your phone.',
        ];
    }
}
