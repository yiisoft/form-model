<?php

declare(strict_types=1);

namespace Yiisoft\FormModel\Tests\Support\Form\Chart;

use Yiisoft\FormModel\FormModel;
use Yiisoft\Hydrator\Attribute\Parameter\Collection;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\Required;

final class ChartSetForm extends FormModel
{
    public function __construct(
        #[Collection(Chart::class)]
        #[Required]
        #[Each([new Nested(Chart::class)])]
        private array $charts = [],
    ) {
    }

    public function getCharts(): array
    {
        return $this->charts;
    }
}
