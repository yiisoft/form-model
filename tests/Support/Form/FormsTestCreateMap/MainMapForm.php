<?php

declare(strict_types=1);

namespace Yiisoft\FormModel\Tests\Support\Form\FormsTestCreateMap;

use Yiisoft\FormModel\FormModel;
use Yiisoft\Validator\Rule\Email;
use Yiisoft\Validator\Rule\FilledAtLeast;
use Yiisoft\Validator\Rule\Integer;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\RulesProviderInterface;

#[FilledAtLeast(['age', 'job'])]
class MainMapForm extends FormModel implements RulesProviderInterface
{
    public function __construct(
        public int $age = 0,
        public string $job = '',
        #[Nested(FirstNestedForm::class)]
        public FirstNestedForm $firstForm = new FirstNestedForm(),
        public Blog $blog = new Blog(),
        public Shop $shop = new Shop(),
    )
    {
    }

    public function getRules(): array
    {
        return [
            'age' => new Integer(min: 5),
            'job' => new Length(min: 2),
            'blog' => [
                new Nested([
                    new FilledAtLeast(['post']),
                    'post' => [
                        'author' => new Nested([
                            'name' => new Required(),
                            'email' => new Email(),
                        ]),
                    ],
                ]),
            ],
            'shop' => new Nested([
                'storage' => [
                    new FilledAtLeast(['name', 'address']),
                    'name' => new Required(),
                ],
            ]),
        ];
    }
}
