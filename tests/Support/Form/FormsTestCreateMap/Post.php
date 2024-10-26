<?php

declare(strict_types=1);

namespace Yiisoft\FormModel\Tests\Support\Form\FormsTestCreateMap;

use Yiisoft\FormModel\FormModel;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\RulesProviderInterface;

class Post extends FormModel implements RulesProviderInterface
{
    public function __construct(
        public string $title = '',
        public string $content = '',
        public Author $author = new Author(),
    )
    {
    }

    public function getRules(): array
    {
        return [
            'title' => new Length(min: 3),
            'content' => new Length(min: 3),
            'author' => new Nested(),
            'author.bio' => new Required(),
        ];
    }
}
