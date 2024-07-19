<?php

declare(strict_types=1);

namespace Yiisoft\FormModel\Tests\Support\Form\Chart;

use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Required;

final class Coordinate
{
    #[Required]
    #[Number(min: -10, max: 10)]
    private int $x;
    #[Required]
    #[Number(min: -10, max: 10)]
    private int $y;
}
