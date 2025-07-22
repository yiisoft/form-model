<?php

declare(strict_types=1);

namespace Yiisoft\FormModel\Tests\Support\Form\FormsTestCreateMap;

use Yiisoft\FormModel\FormModel;
use Yiisoft\Validator\Rule\Email;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\RulesProviderInterface;

class Author extends FormModel implements RulesProviderInterface
{
    public function __construct(
        public string $name = '',
        public string $email = '',
        public string $bio = '',
    ) {
    }

    public function getRules(): array
    {
        return [
            'name' => new Length(min: 3),
            'email' => new Email(),
            'bio' => new Length(min: 3),
        ];
    }
}
