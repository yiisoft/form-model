<?php

declare(strict_types=1);

namespace Yiisoft\FormModel\Tests\Support\Form;

use Yiisoft\FormModel\FormModel;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\RulesProviderInterface;

final class SelectForm extends FormModel implements RulesProviderInterface
{
    private ?int $color = null;
    public ?int $requiredWhen = null;

    public function getRules(): array
    {
        return [
            'color' => [new Required()],
            'requiredWhen' => [new Required(when: static fn() => false)],
        ];
    }
}
