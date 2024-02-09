<?php

declare(strict_types=1);

use Yiisoft\Definitions\DynamicReference;
use Yiisoft\FormModel\FormHydrator;
use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Hydrator\TypeCaster\CompositeTypeCaster;
use Yiisoft\Hydrator\TypeCaster\HydratorTypeCaster;
use Yiisoft\Hydrator\TypeCaster\NullTypeCaster;
use Yiisoft\Hydrator\TypeCaster\PhpNativeTypeCaster;
use Yiisoft\Hydrator\Validator\ValidatingHydrator;

return [
    FormHydrator::class => [
        '__construct()' => [
            'hydrator' => DynamicReference::to([
                'class' => ValidatingHydrator::class,
                '__construct()' => [
                    'hydrator' => DynamicReference::to([
                        'class' => Hydrator::class,
                        '__construct()' => [
                            'typeCaster' => new CompositeTypeCaster(
                                new NullTypeCaster(emptyString: true),
                                new PhpNativeTypeCaster(),
                                new HydratorTypeCaster(),
                            ),
                        ],
                    ])
                ],
            ])
        ],
    ],
];
