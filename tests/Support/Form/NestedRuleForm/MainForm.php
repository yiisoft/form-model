<?php

declare(strict_types=1);

namespace Yiisoft\FormModel\Tests\Support\Form\NestedRuleForm;

use Yiisoft\FormModel\Attribute\Safe;
use Yiisoft\FormModel\FormModel;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\RulesProviderInterface;

final class MainForm extends FormModel implements RulesProviderInterface
{
    #[Safe]
    public string $value = '';

    #[Safe]
    public FirstLevelForm $firstLevelForm;

    public function __construct()
    {
        $this->firstLevelForm = new FirstLevelForm();
    }

    public function getRules(): array
    {
        return [
            'value' => new Required(),
            'firstLevelForm' => new Nested(),
        ];
    }
}
