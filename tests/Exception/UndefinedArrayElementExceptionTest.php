<?php

declare(strict_types=1);

namespace Yiisoft\FormModel\Tests\Exception;

use PHPUnit\Framework\TestCase;
use Yiisoft\FormModel\Exception\UndefinedArrayElementException;

final class UndefinedArrayElementExceptionTest extends TestCase
{
    public function testBase(): void
    {
        $exception = new UndefinedArrayElementException('test');

        $this->assertSame('Undefined array element: "test".', $exception->getMessage());
    }
}
