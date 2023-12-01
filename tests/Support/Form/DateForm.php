<?php

declare(strict_types=1);

namespace Yiisoft\FormModel\Tests\Support\Form;

use Yiisoft\FormModel\FormModel;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\RulesProviderInterface;

final class DateForm extends FormModel implements RulesProviderInterface
{
    private ?string $main = null;
    private ?string $second = null;

    public function getRules(): array
    {
        return [
            'main' => [new Required()],
            'second' => [new Required(when: static fn() => false)],
        ];
    }
}
