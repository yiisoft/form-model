<?php

declare(strict_types=1);

namespace Yiisoft\FormModel\Tests\Support\Form\FormsTestCreateMap;

use Yiisoft\FormModel\FormModel;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\RulesProviderInterface;

class Storage extends FormModel implements RulesProviderInterface
{
    public function __construct(
        public string $name = '',
        public string $address = '',
        public string $phone = '',
    ) {
    }

    public function getRules(): array
    {
        return [
            'name' => new Length(min: 3),
            'address' => new Length(min: 3),
            'phone' => new Length(min: 3),
        ];
    }
}