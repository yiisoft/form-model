<?php

declare(strict_types=1);

namespace Yiisoft\FormModel\Tests\Support\Form\NestedRuleForm;

use Yiisoft\FormModel\FormModel;
use Yiisoft\Validator\Rule\Integer;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\RulesProviderInterface;

final class FirstLevelForm extends FormModel implements RulesProviderInterface
{
    private int $number = 1;
    private SecondLevelForm $secondLevelForm;

    public function getRules(): array
    {
        return [
            'number' => new Integer(min: 1),
            'secondLevelForm' => new Nested(),
        ];
    }
}
