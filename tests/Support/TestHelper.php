<?php

declare(strict_types=1);

namespace Yiisoft\FormModel\Tests\Support;

use Closure;
use ReflectionFunction;
use ReflectionParameter;
use Yiisoft\FormModel\FormHydrator;
use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Hydrator\TypeCaster\TypeCastContext;
use Yiisoft\Validator\Validator;

final class TestHelper
{
    public static function createFormHydrator(): FormHydrator
    {
        $validator = new Validator();
        return new FormHydrator(
            new Hydrator(),
            $validator,
        );
    }

    public static function getFirstParameter(Closure $closure): ReflectionParameter
    {
        $parameters = (new ReflectionFunction($closure))->getParameters();
        return reset($parameters);
    }

    public static function createTypeCastContext(Closure $closure): TypeCastContext
    {
        return new TypeCastContext(
            new Hydrator(),
            self::getFirstParameter($closure),
        );
    }
}
