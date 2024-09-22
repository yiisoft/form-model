<?php

declare(strict_types=1);

namespace Yiisoft\FormModel\Tests\Support\Form\Chart;

use Yiisoft\FormModel\FormModel;
use Yiisoft\Hydrator\Attribute\Parameter\Collection;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\Required;

final class Chart extends FormModel
{
    public function __construct(
        #[Collection(Point::class)]
        #[Required]
        #[Each([new Nested(Point::class)])]
        private array $points,
    ) {
    }
}
