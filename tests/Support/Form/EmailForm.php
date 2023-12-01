<?php

declare(strict_types=1);

namespace Yiisoft\FormModel\Tests\Support\Form;

use Yiisoft\FormModel\FormModel;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Regex;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\RulesProviderInterface;

final class EmailForm extends FormModel implements RulesProviderInterface
{
    public ?string $cto = null;
    public ?string $teamlead = null;
    public ?string $code = null;
    public ?string $nocode = null;
    public ?string $requiredWhen = null;

    public function getRules(): array
    {
        return [
            'cto' => [new Required()],
            'teamlead' => [new Length(min: 10, max: 199)],
            'code' => [new Regex(pattern: '~\w+@\w+~')],
            'nocode' => [new Regex(pattern: '~\w+@\w+~', not: true)],
            'requiredWhen' => [new Required(when: static fn () => false)],
        ];
    }
}
