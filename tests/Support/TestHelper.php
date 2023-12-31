<?php

declare(strict_types=1);

namespace Yiisoft\FormModel\Tests\Support;

use Yiisoft\FormModel\FormHydrator;
use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Hydrator\Validator\Attribute\ValidateResolver;
use Yiisoft\Hydrator\Validator\ValidatingHydrator;
use Yiisoft\Validator\Validator;

final class TestHelper
{
    public static function createFormHydrator(): FormHydrator
    {
        $validator = new Validator();
        return new FormHydrator(
            new ValidatingHydrator(
                new Hydrator(),
                $validator,
                new ValidateResolver($validator),
            ),
            $validator,
        );
    }
}
