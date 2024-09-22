<?php

declare(strict_types=1);

namespace Yiisoft\FormModel\Tests\Support\Form\NestedForm;

use Yiisoft\FormModel\FormModel;
use Yiisoft\Hydrator\Attribute\Parameter\Collection;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\Required;

final class PostCategory extends FormModel
{
    public function __construct(
        #[Required]
        #[Length(max: 255)]
        private string $name = '',
        #[Collection(Post::class)]
        #[Each([new Nested(Post::class)])]
        private array $posts = [],
    ) {
    }
}
