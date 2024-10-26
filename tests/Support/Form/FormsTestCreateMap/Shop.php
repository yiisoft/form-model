<?php

declare(strict_types=1);

namespace Yiisoft\FormModel\Tests\Support\Form\FormsTestCreateMap;

use Yiisoft\FormModel\FormModel;
use Yiisoft\Validator\Rule\FilledAtLeast;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\RulesProviderInterface;

class Shop extends FormModel implements RulesProviderInterface
{


    public function __construct(
        public string $name = '',
        public string $address = '',
        public string $phone = '',
        public Storage $storage = new Storage(),
        public readonly string $readonly = '',
    )
    {
    }

    public function getRules(): array
    {
        return [
            new Required(),
            'name' => new Length(min: 3),
            'address' => new Length(min: 3),
            'phone' => [new Length(min: 3)],
            'storage' => [
                new FilledAtLeast(['name', 'address']),
                'name' => new Required(),
                'address' => new Required(),
            ],
        ];
    }
}
