<?php

declare(strict_types=1);

namespace Yiisoft\FormModel\Tests;

use HttpSoft\Message\ServerRequestFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\FormModel\FormModel;
use Yiisoft\FormModel\Tests\Support\Form\CarForm;
use Yiisoft\FormModel\Tests\Support\Form\PopulateNestedForm\MainForm;
use Yiisoft\FormModel\Tests\Support\TestHelper;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Integer;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\RulesProviderInterface;

final class FormHydratorTest extends TestCase
{
    public function testPopulateWithStrictMap(): void
    {
        $form = new class () extends FormModel {
            public int $a = 0;
            public int $b = 0;
        };
        $data = ['x' => 1, 'y' => 2];
        $map = ['a' => 'x', 'b' => 'y'];

        TestHelper::createFormHydrator()->populate($form, $data, $map, true);

        $this->assertSame(1, $form->a);
        $this->assertSame(2, $form->b);
    }

    public static function dataValidate(): array
    {
        return [
            'empty data' => [[], ['name' => ['Name must contain at least 3 characters.']]],
            'invalid data' => [
                ['CarForm' => ['name' => 'A']],
                ['name' => ['Name must contain at least 3 characters.']],
            ],
            'valid data' => [['CarForm' => ['name' => 'Test']], []],
        ];
    }

    #[DataProvider('dataValidate')]
    public function testPopulateAndValidateSeparately(array $data, array $expectedErrorMessages): void
    {
        $form = new CarForm();
        $formHydrator = TestHelper::createFormHydrator();

        $formHydrator->populate($form, $data);
        $result = $formHydrator->validate($form);

        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals($expectedErrorMessages, $result->getErrorMessagesIndexedByPath());
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
    public function testPopulateAndValidate(bool $expected, mixed $data): void
    {
        $form = new CarForm();

        $result = TestHelper::createFormHydrator()->populateAndValidate($form, $data);

        $this->assertSame($expected, $result);
    }

    public static function dataPopulateFromPost(): array
    {
        $factory = new ServerRequestFactory();

        return [
            'non-post' => [
                false,
                $factory->createServerRequest('GET', '/'),
            ],
            'empty-data' => [
                false,
                $factory->createServerRequest('POST', '/'),
            ],
            'invalid-data' => [
                true,
                $factory->createServerRequest('POST', '/')->withParsedBody(['CarForm' => ['name' => 'A']]),
            ],
            'valid-data' => [
                true,
                $factory->createServerRequest('POST', '/')->withParsedBody(['CarForm' => ['name' => 'TEST']]),
            ],
        ];
    }

    #[DataProvider('dataPopulateFromPost')]
    public function testPopulateFromPost(bool $expected, ServerRequestInterface $request): void
    {
        $form = new CarForm();

        $result = TestHelper::createFormHydrator()->populateFromPost($form, $request);

        $this->assertSame($expected, $result);
    }

    public static function dataPopulateFromPostAndValidate(): array
    {
        $factory = new ServerRequestFactory();

        return [
            'non-post' => [
                false,
                $factory->createServerRequest('GET', '/'),
            ],
            'empty-data' => [
                false,
                $factory->createServerRequest('POST', '/'),
            ],
            'invalid-data' => [
                false,
                $factory->createServerRequest('POST', '/')->withParsedBody(['CarForm' => ['name' => 'A']]),
            ],
            'valid-data' => [
                true,
                $factory->createServerRequest('POST', '/')->withParsedBody(['CarForm' => ['name' => 'TEST']]),
            ],
        ];
    }

    #[DataProvider('dataPopulateFromPostAndValidate')]
    public function testPopulateFromPostAndValidate(bool $expected, ServerRequestInterface $request): void
    {
        $form = new CarForm();

        $result = TestHelper::createFormHydrator()->populateFromPostAndValidate($form, $request);

        $this->assertSame($expected, $result);
    }

    public static function dataNestedPopulate(): array
    {
        $factory = new ServerRequestFactory();
        $expected = [
            'value' => 'mainProperty',
            'firstForm' => 'firstTest',
            'secondForm' => 3,
            'secondForm.string' => 'secondFormString',
        ];
        return [
            'nested-array-data' => [
                $expected,
                $factory->createServerRequest('POST', '/')->withParsedBody([
                    'MainForm' => [
                        'value' => $expected['value'],
                        'firstForm' => [
                            'value' => $expected['firstForm'],
                            'secondForm' => [
                                'value' => $expected['secondForm'],
                                'string' => $expected['secondForm.string']
                            ],
                        ],
                    ],
                ]),
            ],
            'dot-notation-data' => [
                $expected,
                $factory->createServerRequest('POST', '/')->withParsedBody([
                    'MainForm' => [
                        'value' => $expected['value'],
                        'firstForm.value' => $expected['firstForm'],
                        'firstForm.secondForm.value' => $expected['secondForm'],
                        'firstForm.secondForm.string' => $expected['secondForm.string']
                    ],
                ]),
            ],
            'one-level-array-data' => [
                $expected,
                $factory->createServerRequest('POST', '/')->withParsedBody([
                    'MainForm' => ['value' => $expected['value']],
                    'FirstNestedForm' => ['value' => $expected['firstForm']],
                    'SecondNestedForm' => ['value' => $expected['secondForm'], 'string' => $expected['secondForm.string']],
                ]),
            ],
            'mixed-one-level-and-dot-notation-data' => [
                $expected,
                $factory->createServerRequest('POST', '/')->withParsedBody([
                    'MainForm' => [
                        'value' => $expected['value'],
                        'firstForm.secondForm.string' => $expected['secondForm.string'],
                    ],
                    'FirstNestedForm' => [
                        'value' => $expected['firstForm'],
                        'secondForm.value' => $expected['secondForm'],
                    ],
                ]),
            ],
            'mixed-one-level-and-nested-array-data' => [
                $expected,
                $factory->createServerRequest('POST', '/')->withParsedBody([
                    'MainForm' => [
                        'value' => $expected['value'],
                        'firstForm' => [
                            'value' => $expected['firstForm'],
                            'secondForm' => [
                                'string' => $expected['secondForm.string'],
                            ],
                        ],
                    ],
                    'SecondNestedForm' => [
                        'value' => $expected['secondForm'],
                    ],
                ]),
            ],
        ];
    }

    #[DataProvider('dataNestedPopulate')]
    public function testPopulateNestedFormFromPost(array $expected, ServerRequestInterface $request): void
    {
        $form = new MainForm();

        TestHelper::createFormHydrator()->populateFromPost($form, $request);
        $this->assertSame($expected['value'], $form->value);
        $this->assertSame($expected['firstForm'], $form->firstForm->value);
        $this->assertSame($expected['secondForm'], $form->firstForm->secondForm->value);
        $this->assertSame($expected['secondForm.string'], $form->firstForm->secondForm->string);
    }

    public function testPopulateFormWithRulesFromAttributesAndMethod(): void
    {
        $form = new class () extends FormModel implements RulesProviderInterface {
            #[Length(min: 3)]
            public string $name = '';

            public ?int $age = null;

            #[Required]
            public string $job = '';

            public string $tip = '';

            public function getRules(): iterable
            {
                return [
                    'age' => new Integer(min: 17),
                    'job' => new Length(min: 2),
                ];
            }
        };

        TestHelper::createFormHydrator()->populate($form, [
            'name' => 'Sergei',
            'age' => 38,
            'job' => 'developer',
            'tip' => 'test',
        ]);

        $this->assertSame('Sergei', $form->name);
        $this->assertSame(38, $form->age);
        $this->assertSame('developer', $form->job);
        $this->assertSame('', $form->tip);
    }
}
