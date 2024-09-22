<?php

declare(strict_types=1);

namespace Yiisoft\FormModel\Tests\Support\Form\NestedForm;

use Yiisoft\FormModel\FormModel;
use Yiisoft\Validator\Rule\Integer;
use Yiisoft\Validator\Rule\Required;

final class User extends FormModel
{
    public function __construct(
        #[Required]
        #[Integer(min: 1)]
        private int $id,
    ) {
    }
}
