<?php

declare(strict_types=1);

namespace Yiisoft\FormModel\Tests;

use Closure;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Yiisoft\FormModel\NonArrayTypeCaster;
use Yiisoft\FormModel\Tests\Support\TestHelper;

final class NonArrayTypeCasterTest extends TestCase
{
    public static function dataBase(): array
    {
        return [
            [true, '', static fn(array $a) => null],
            [true, 'test', static fn(array $a) => null],
            [true, 0, static fn(array $a) => null],
            [true, 42, static fn(array $a) => null],
            [false, '', static fn(string $a) => null],
            [false, '', static fn(array|string $a) => null],
            [false, [], static fn(array $a) => null],
            [false, ['a'], static fn(array $a) => null],
        ];
    }

    #[DataProvider('dataBase')]
    public function testBase(bool $success, mixed $value, Closure $closure): void
    {
        $typeCaster = new NonArrayTypeCaster();
        $context = TestHelper::createTypeCastContext($closure);

        $result = $typeCaster->cast($value, $context);

        $this->assertSame($success, $result->isResolved());
        if ($success) {
            $this->assertSame([], $result->getValue());
        }
    }
}
