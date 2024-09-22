<?php

declare(strict_types=1);

namespace Yiisoft\FormModel\Tests\Support\Form\NestedForm;

use Yiisoft\FormModel\FormModel;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Rule\Type\StringType;

final class Post extends FormModel
{
    public function __construct(
        #[Required]
        #[Length(max: 255)]
        private string $name,
        #[StringType]
        private string $description = '',
        // #[Required]
        private User $author,
    ) {
    }
}
