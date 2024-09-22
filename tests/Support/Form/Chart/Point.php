<?php

declare(strict_types=1);

namespace Yiisoft\FormModel\Tests\Support\Form\Chart;

use Yiisoft\FormModel\FormModel;
use Yiisoft\Validator\Rule\Count;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Required;

final class Point extends FormModel
{
    public function __construct(
        #[Required]
        #[Nested(Coordinates::class)]
        private Coordinates $coordinates,
        #[Required]
        #[Count(exactly: 3)]
        #[Each([new Number(min: 0, max: 255)])]
        private array $rgb,
    ) {
    }
}