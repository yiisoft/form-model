<?php

declare(strict_types=1);

namespace Yiisoft\FormModel\Tests;

use HttpSoft\Message\ServerRequestFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\FormModel\FormModel;
use Yiisoft\FormModel\Tests\Support\Form\CarForm;
use Yiisoft\FormModel\Tests\Support\TestHelper;
use Yiisoft\Validator\Result;

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
}
