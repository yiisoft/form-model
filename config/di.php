<?php

declare(strict_types=1);

use Yiisoft\Definitions\Reference;
use Yiisoft\FormModel\FormHydrator;
use Yiisoft\Hydrator\Validator\ValidatingHydrator;

return [
    FormHydrator::class => [
        '__construct()' => [
            'hydrator' => Reference::to(ValidatingHydrator::class),
        ],
    ],
];
