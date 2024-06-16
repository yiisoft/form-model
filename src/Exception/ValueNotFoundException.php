<?php

declare(strict_types=1);

namespace Yiisoft\FormModel\Exception;

use InvalidArgumentException;

/**
 * Thrown when value isn't found.
 */
abstract class ValueNotFoundException extends InvalidArgumentException
{
}
