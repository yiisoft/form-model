<?php

declare(strict_types=1);

namespace Yiisoft\FormModel\Tests;

use HttpSoft\Message\ServerRequestFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\FormModel\FormModel;
use Yiisoft\FormModel\Tests\Support\Form\CarForm;
use Yiisoft\FormModel\Tests\Support\Form\FormsTestCreateMap\MainMapForm;
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
                                'string' => $expected['secondForm.string'],
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
                        'firstForm.secondForm.string' => $expected['secondForm.string'],
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
        $this->assertSame($expected['firstForm'], $form->firstNestedForm()->value);
        $this->assertSame($expected['secondForm'], $form->firstNestedForm()->secondForm()->value);
        $this->assertSame($expected['secondForm.string'], $form->firstNestedForm()->secondForm()->string);
    }

    public static function dataNestedFormsCreateMap(): array
    {
        return [
            'array-data' => [
                [
                    'MainMapForm' => [
                        'age' => 38,
                        'job' => 'developer',
                        'firstForm' => [
                            'value' => 'value',
                            'secondForm' => [
                                'post' => 'post',
                                'author' => 'author',
                            ],
                        ],
                        'blog' => [
                            'title' => 'title',
                            'description' => 'description',
                            'post' => [
                                'title' => 'title',
                                'content' => 'content',
                                'author' => [
                                    'name' => 'author',
                                    'email' => 'author@yiisoft.com',
                                    'bio' => 'My bio',
                                ],
                            ],
                        ],
                        'shop' => [
                            'name' => 'shop',
                            'address' => 'address',
                            'phone' => 'phone',
                            'storage' => [
                                'name' => 'storage',
                                'address' => 'address',
                                'phone' => 'phone',
                            ],
                        ],
                    ],
                ],
            ],
            'dot-notation-data' => [
                [
                    'MainMapForm' => [
                        'age' => 38,
                        'job' => 'developer',
                        'firstForm.value' => 'value',
                        'firstForm.secondForm.post' => 'post',
                        'firstForm.secondForm.author' => 'author',
                        'blog.title' => 'title',
                        'blog.description' => 'description',
                        'blog.post.title' => 'title',
                        'blog.post.content' => 'content',
                        'blog.post.author.name' => 'author',
                        'blog.post.author.email' => 'author@yiisoft.com',
                        'blog.post.author.bio' => 'My bio',
                        'shop.name' => 'shop',
                        'shop.address' => 'address',
                        'shop.phone' => 'phone',
                        'shop.storage.name' => 'storage',
                        'shop.storage.address' => 'address',
                        'shop.storage.phone' => 'phone',
                    ],
                ],
            ],

        ];
    }

    #[DataProvider('dataNestedFormsCreateMap')]
    public function testPopulateNestedFormsWithCreateMap(array $data): void
    {
        $form = new MainMapForm();

        TestHelper::createFormHydrator()->populate($form, $data);

        $this->assertSame(38, $form->age);
        $this->assertSame('developer', $form->job);
        $this->assertSame('title', $form->blog->post->title);
        $this->assertSame('content', $form->blog->post->content);
        $this->assertSame('author', $form->blog->post->author->name);
        $this->assertSame('author@yiisoft.com', $form->blog->post->author->email);
        $this->assertSame('My bio', $form->blog->post->author->bio);
        $this->assertSame('shop', $form->shop->name);
        $this->assertSame('address', $form->shop->address);
        $this->assertSame('phone', $form->shop->phone);
        $this->assertSame('storage', $form->shop->storage->name);
        $this->assertSame('address', $form->shop->storage->address);
        $this->assertSame('phone', $form->shop->storage->phone);
        $this->assertSame('value', $form->firstForm->value);
        $this->assertSame('post', $form->firstForm->secondForm->post);
        $this->assertSame('author', $form->firstForm->secondForm->author);
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
