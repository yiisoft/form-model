<?php

declare(strict_types=1);

namespace Yiisoft\FormModel\Tests\Support\Form\NestedRuleForm;

use Yiisoft\FormModel\FormModel;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\RulesProviderInterface;

final class SecondLevelForm extends FormModel implements RulesProviderInterface
{
    private float $float = 0.01;

    public function getRules(): array
    {
        return [
            'float' => new Number(min: 0),
        ];
    }
}
