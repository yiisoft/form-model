<?php

declare(strict_types=1);

namespace Yiisoft\FormModel\Tests;

use PHPUnit\Framework\TestCase;
use Yiisoft\FormModel\Safe;

final class SafeTest extends TestCase
{
    public function testBase(): void
    {
        $safe = new Safe();

        $this->assertSame(Safe::class, $safe->getName());
        $this->assertSame($safe, $safe->getHandler());
    }
}
