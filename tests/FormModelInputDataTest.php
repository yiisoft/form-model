<?php

declare(strict_types=1);

namespace Yiisoft\FormModel\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Yiisoft\FormModel\FormModel;
use Yiisoft\FormModel\FormModelInputData;
use Yiisoft\FormModel\FormModelInterface;
use Yiisoft\FormModel\Tests\Support\Form\FormWithNestedStructures;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Validator;

use function PHPUnit\Framework\assertSame;

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
                new class () extends FormModel {
                    public int $age = 21;
                },
                'age',
            ],
            'unicode-property' => [
                'ВОЗРАСТ',
                'возраст',
                new class () extends FormModel {
                    public int $ВОЗРАСТ = 21;
                },
                'ВОЗРАСТ',
            ],
        ];
    }

    #[DataProvider('dataNameAndId')]
    public function testNameAndId(?string $expectedName, ?string $expectedId, FormModel $form, string $name): void
    {
        $inputData = new FormModelInputData($form, $name);

        $this->assertSame($expectedName, $inputData->getName());
        $this->assertSame($expectedId, $inputData->getId());
    }

    public function testEmptyFormNameForTabularInputs(): void
    {
        $form = new class () extends FormModel {
            public array $age = [];
        };
        $inputData = new FormModelInputData($form, '[0]age');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Form name cannot be empty for tabular inputs.');
        $inputData->getName();
    }

    public function testUnicodePropertyName(): void
    {
        $form = new class () extends FormModel {
            public array $возраст = [];
        };
        $inputData = new FormModelInputData($form, 'возраст');

        $this->assertSame('Возраст', $inputData->getLabel());
    }

    public function testNotExistProperty(): void
    {
        $form = new class () extends FormModel {
        };
        $inputData = new FormModelInputData($form, 'age');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Property "age" does not exist.');
        $inputData->getLabel();
    }

    public function testInvalidProperty(): void
    {
        $form = new class () extends FormModel {
        };

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Property name must contain word characters only.');
        new FormModelInputData($form, 'new age');
    }

    public static function dataIsValid(): array
    {
        $validator = new Validator();

        $form = new class () extends FormModel {
            #[Required]
            public ?string $name = null;
        };

        $validForm = clone $form;
        $validForm->name = 'test';
        $validator->validate($validForm);

        $invalidForm = clone $form;
        $validator->validate($invalidForm);

        return [
            'non-validated' => [false, $form],
            'valid' => [true, $validForm],
            'invalid' => [true, $invalidForm],
        ];
    }

    #[DataProvider('dataIsValid')]
    public function testIsValidated(bool $expected, FormModelInterface $form): void
    {
        $inputData = new FormModelInputData($form, 'name');
        $this->assertSame($expected, $inputData->isValidated());
    }

    public static function dataGetValidationErrors(): iterable
    {
        yield 'non-exist' => [
            'property' => 'another-name',
            'expectedErrors' => [],
        ];
        yield 'single' => [
            'property' => 'name',
            'expectedErrors' => ['Name cannot be blank.'],
        ];
        yield 'summary' => [
            'property' => 'amounts',
            'expectedErrors' => ['Amounts cannot be blank.', 'Amounts must be no greater than 50.'],
        ];
        yield 'nested' => [
            'property' => 'amounts[usd]',
            'expectedErrors' => ['Amounts must be no greater than 50.'],
        ];
    }

    #[DataProvider('dataGetValidationErrors')]
    public function testGetValidationErrors(string $property, array $expectedErrors): void
    {
        $form = new class () extends FormModel {
            #[Required]
            public ?string $name = null;

            #[Each([
                new Required(),
                new Number(max: 50, skipOnEmpty: true),
            ])]
            public array $amounts = [
                'rub' => '',
                'usd' => '100',
            ];
        };
        (new Validator())->validate($form);

        $inputData = new FormModelInputData($form, $property);

        assertSame($expectedErrors, $inputData->getValidationErrors());
    }
}
