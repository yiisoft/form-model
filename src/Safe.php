<?php

declare(strict_types=1);

namespace Yiisoft\FormModel;

use Attribute;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\ValidationContext;

/**
 * Safe validation rule marks a model property as safe for filling with {@see FormHydrator::populate()},
 * {@see FormHydrator::populateAndValidate()}, {@see FormHydrator::populateFromPost()},
 * and {@see FormHydrator::populateFromPostAndValidate()}.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class Safe implements RuleInterface, RuleHandlerInterface
{
    public function validate(mixed $value, object $rule, ValidationContext $context): Result
    {
        return new Result();
    }

    public function getName(): string
    {
        return self::class;
    }

    public function getHandler(): RuleHandlerInterface
    {
        return $this;
    }
}
