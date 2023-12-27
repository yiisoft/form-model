<?php

declare(strict_types=1);

namespace Yiisoft\FormModel\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Yiisoft\FormModel\FormModel;
use Yiisoft\FormModel\FormModelInputData;
use Yiisoft\FormModel\Tests\Support\Form\FormWithNestedStructures;

final class FormModelInputDataTest extends TestCase
{
    public static function dataNameAndId(): array
    {
        return [
            [
                'FormWithNestedStructures[coordinates][latitude]',
                'formwithnestedstructures-coordinates-latitude',
                new FormWithNestedStructures(),
                'coordinates[latitude]',
            ],
            [
                'FormWithNestedStructures[array][nested][value]',
                'formwithnestedstructures-array-nested-value',
                new FormWithNestedStructures(),
                'array[nested][value]',
            ],
            'anonymous-form' => [
                'age',
                'age',
                new class() extends FormModel {
                    public int $age = 21;
                },
                'age',
            ]
        ];
    }

    #[DataProvider('dataNameAndId')]
    public function testNameAndId(?string $expectedName, ?string $expectedId, FormModel $form, string $name): void
    {
        $inputData = new FormModelInputData($form, $name);

        $this->assertSame($expectedName, $inputData->getName());
        $this->assertSame($expectedId, $inputData->getId());
    }
}
