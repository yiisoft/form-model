<?php

declare(strict_types=1);

namespace Yiisoft\FormModel;

use ReflectionNamedType;
use ReflectionType;
use Yiisoft\Hydrator\Result;
use Yiisoft\Hydrator\TypeCaster\TypeCastContext;
use Yiisoft\Hydrator\TypeCaster\TypeCasterInterface;

/**
 *
 */
final class NonArrayTypeCaster implements TypeCasterInterface
{
    public function cast(mixed $value, TypeCastContext $context): Result
    {
        if (is_array($value)) {
            return Result::fail();
        }

        if ($this->isArray($context->getReflectionType())) {
            return Result::success([]);
        }

        return Result::fail();
    }

    private function isArray(?ReflectionType $type): bool
    {
        return $type instanceof ReflectionNamedType && $type->isBuiltin() && $type->getName() === 'array';
    }
}
