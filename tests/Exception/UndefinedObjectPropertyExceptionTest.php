<?php

declare(strict_types=1);

namespace Yiisoft\FormModel\Tests\Exception;

use PHPUnit\Framework\TestCase;
use Yiisoft\FormModel\Exception\UndefinedObjectPropertyException;

final class UndefinedObjectPropertyExceptionTest extends TestCase
{
    public function testBase(): void
    {
        $exception = new UndefinedObjectPropertyException('test');

        $this->assertSame(
            'Undefined object property: "test".',
            $exception->getMessage()
        );
    }
}
