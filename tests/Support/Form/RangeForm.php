<?php

declare(strict_types=1);

namespace Yiisoft\FormModel\Tests\Support\Form;

use Yiisoft\FormModel\FormModel;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\RulesProviderInterface;

final class RangeForm extends FormModel implements RulesProviderInterface
{
    private int $volume = 23;
    private ?int $count = null;
    public ?int $requiredWhen = null;

    public function getRules(): array
    {
        return [
            'volume' => [new Required()],
            'count' => [new Number(min: 1, max: 9)],
            'requiredWhen' => [
                new Required(when: static fn () => false),
                new Number(min: 1),
            ],
        ];
    }

    public function getPropertyLabels(): array
    {
        return [
            'volume' => 'Volume level',
        ];
    }
}
