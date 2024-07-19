<?php

declare(strict_types=1);

namespace Yiisoft\FormModel\Tests\Support\Form\Chart;

use Yiisoft\FormModel\FormModel;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\Required;

final class ChartForm extends FormModel
{
    #[Required]
    #[Each([new Nested(Point::class)])]
    private array $points = [];
}
