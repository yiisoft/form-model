<?php

declare(strict_types=1);

namespace Yiisoft\FormModel\Tests\Support\Form\FormsTestCreateMap;

use Yiisoft\FormModel\FormModel;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\RulesProviderInterface;

class Blog extends FormModel implements RulesProviderInterface
{
    public function __construct(
        public string $title = '',
        public string $description = '',
        public Post $post = new Post(),
    ) {
    }

    public function getRules(): array
    {
        return [
            'title' => new Length(min: 3),
            'description' => new Length(min: 3),
            'post' => new Nested([
                'title' => new Required(),
            ]),
        ];
    }
}
