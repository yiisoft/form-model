<?php

declare(strict_types=1);

namespace Yiisoft\FormModel;

use LogicException;
use Yiisoft\Validator\PostValidationHookInterface;
use Yiisoft\Validator\Result;

/**
 * Used for objects that can be validated. It provides a method to get validation result.
 */
interface ValidatedInputInterface extends PostValidationHookInterface
{
    /**
     * Returns validation result.
     *
     * @throws LogicException When validation result is not set.
     * @return Result Validation result.
     */
    public function getValidationResult(): Result;
}
