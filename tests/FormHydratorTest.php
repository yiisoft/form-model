<?php

declare(strict_types=1);

namespace Yiisoft\FormModel\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Yiisoft\FormModel\FormModel;
use Yiisoft\FormModel\Tests\Support\Form\CarForm;
use Yiisoft\FormModel\Tests\Support\TestHelper;

final class FormHydratorTest extends TestCase
{
    public function testPopulateWithStrictMap(): void
    {
        $form = new class() extends FormModel {
            public int $a = 0;
            public int $b = 0;
        };
        $data = ['x' => 1, 'y' => 2];
        $map = ['a' => 'x', 'b' => 'y'];

        TestHelper::createFormHydrator()->populate($form, $data, $map, true);

        $this->assertSame(1, $form->a);
        $this->assertSame(2, $form->b);
    }

    public static function dataPopulateAndValidate(): array
    {
        return [
            'empty-data' => [false, []],
            'invalid-data' => [false, ['CarForm' => ['name' => 'A']]],
            'valid-data' => [true, ['CarForm' => ['name' => 'Test']]],
        ];
    }

    #[DataProvider('dataPopulateAndValidate')]
    public function testPopulateAndValidate(bool $expected, mixed $data)
    {
        $form = new CarForm();

        $result = TestHelper::createFormHydrator()->populateAndValidate($form, $data);

        $this->assertSame($expected, $result);
    }
}
