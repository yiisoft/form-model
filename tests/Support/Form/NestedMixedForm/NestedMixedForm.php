<?php

declare(strict_types=1);

namespace Yiisoft\FormModel\Tests\Support\Form\NestedMixedForm;

use Yiisoft\FormModel\FormModel;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\Regex;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\RulesProviderInterface;

final class NestedMixedForm extends FormModel implements RulesProviderInterface
{
    private Body $body;

    public function getRules(): iterable
    {
        return [
            'body' => new Nested([
                'shipping' => [
                    new Required(),
                    new Nested(
                        [
                            'phone' => new Regex('/^\+\d{11}$/', message: 'Invalid phone.'),
                        ],
                        skipOnEmpty: true
                    ),
                ],
            ]),
        ];
    }
}
