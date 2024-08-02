<?php

declare(strict_types=1);

namespace Yiisoft\FormModel\Tests\Support\Form\Chart;

use Yiisoft\Hydrator\Attribute\Parameter\Collection;
use Yiisoft\Validator\Rule\Count;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Required;

final class Point
{
    public function __construct(
        #[Collection(Coordinates::class)]
        #[Required]
        #[Each([new Nested(Coordinates::class)])]
        private Coordinates $coordinates,
        #[Required]
        #[Count(exactly: 3)]
        #[Each([new Number(min: 0, max: 255)])]
        private array $rgb,
    ) {
    }
}
