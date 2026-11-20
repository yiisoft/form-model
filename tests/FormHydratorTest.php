<?php

declare(strict_types=1);

namespace Yiisoft\FormModel\Tests;

use HttpSoft\Message\ServerRequestFactory;
use HttpSoft\Message\UploadedFile;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use stdClass;
use Yiisoft\FormModel\FormModel;
use Yiisoft\FormModel\Tests\Support\Form\CarForm;
use Yiisoft\FormModel\Tests\Support\Form\UploadForm;
use Yiisoft\FormModel\Tests\Support\TestHelper;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Image\Image;
use Yiisoft\Validator\Rule\Integer;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\RulesProviderInterface;

use const UPLOAD_ERR_INI_SIZE;
use const UPLOAD_ERR_NO_FILE;
use const UPLOAD_ERR_OK;

final class FormHydratorTest extends TestCase
{
    public function testPopulateWithStrictMap(): void
    {
        $form = new class extends FormModel {
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

    public function testPopulateAndValidateWithNonArrayData(): void
    {
        $form = new CarForm();
        $form->name = 'ValidData';

        $result = TestHelper::createFormHydrator()->populateAndValidate($form, null);

        $this->assertFalse($result);
    }

    public static function dataPopulateFromGet(): array
    {
        $factory = new ServerRequestFactory();

        return [
            'non-get' => [
                false,
                $factory->createServerRequest('POST', '/'),
            ],
            'empty-data' => [
                false,
                $factory->createServerRequest('GET', '/'),
            ],
            'invalid-data' => [
                true,
                $factory
                    ->createServerRequest('GET', '/')
                    ->withQueryParams(['CarForm' => ['name' => 'A']]),
            ],
            'valid-data' => [
                true,
                $factory
                    ->createServerRequest('GET', '/')
                    ->withQueryParams(['CarForm' => ['name' => 'TEST']]),
            ],
            'post-with-query-params' => [
                false,
                $factory
                    ->createServerRequest('POST', '/')
                    ->withQueryParams(['CarForm' => ['name' => 'TEST']]),
            ],
        ];
    }

    #[DataProvider('dataPopulateFromGet')]
    public function testPopulateFromGet(bool $expected, ServerRequestInterface $request): void
    {
        $form = new CarForm();

        $result = TestHelper::createFormHydrator()->populateFromGet($form, $request);

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

    public static function dataPopulateFromGetAndValidate(): array
    {
        $factory = new ServerRequestFactory();

        return [
            'non-get' => [
                false,
                $factory->createServerRequest('POST', '/'),
            ],
            'empty-data' => [
                false,
                $factory->createServerRequest('GET', '/'),
            ],
            'invalid-data' => [
                false,
                $factory
                    ->createServerRequest('GET', '/')
                    ->withQueryParams(['CarForm' => ['name' => 'A']]),
            ],
            'valid-data' => [
                true,
                $factory
                    ->createServerRequest('GET', '/')
                    ->withQueryParams(['CarForm' => ['name' => 'TEST']]),
            ],
            'post-with-query-params' => [
                false,
                $factory
                    ->createServerRequest('POST', '/')
                    ->withQueryParams(['CarForm' => ['name' => 'TEST']]),
            ],
        ];
    }

    #[DataProvider('dataPopulateFromGetAndValidate')]
    public function testPopulateFromGetAndValidate(bool $expected, ServerRequestInterface $request): void
    {
        $form = new CarForm();

        $result = TestHelper::createFormHydrator()->populateFromGetAndValidate($form, $request);

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

    public function testPopulateFormWithRulesFromAttributesAndMethod(): void
    {
        $form = new class extends FormModel implements RulesProviderInterface {
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

    public function testPopulateFromPostWithFilesMergesBodyAndFiles(): void
    {
        $form = new UploadForm();
        $picture = self::createUploadedFile();
        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', '/')
            ->withParsedBody(['UploadForm' => ['title' => 'Holiday']])
            ->withUploadedFiles(['UploadForm' => ['picture' => $picture]]);

        $result = TestHelper::createFormHydrator()->populateFromPostWithFiles($form, $request);

        $this->assertTrue($result);
        $this->assertSame('Holiday', $form->title);
        $this->assertSame($picture, $form->picture);
    }

    public function testPopulateFromPostWithFilesWithFilesOnly(): void
    {
        $form = new UploadForm();
        $picture = self::createUploadedFile();
        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', '/')
            ->withUploadedFiles(['UploadForm' => ['picture' => $picture]]);

        $result = TestHelper::createFormHydrator()->populateFromPostWithFiles($form, $request);

        $this->assertTrue($result);
        $this->assertSame($picture, $form->picture);
    }

    public function testPopulateFromPostWithFilesSkipsNotUploadedFiles(): void
    {
        $form = new UploadForm();
        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', '/')
            ->withParsedBody(['UploadForm' => ['title' => 'Holiday']])
            ->withUploadedFiles([
                'UploadForm' => [
                    'picture' => self::createUploadedFile(UPLOAD_ERR_NO_FILE),
                    'attachment' => self::createUploadedFile(UPLOAD_ERR_NO_FILE),
                ],
            ]);

        $result = TestHelper::createFormHydrator()->populateFromPostWithFiles($form, $request);

        $this->assertTrue($result);
        $this->assertSame('Holiday', $form->title);
        $this->assertNull($form->picture);
        $this->assertNull($form->attachment);
    }

    public function testPopulateFromPostWithFilesKeepsFilesWithOtherErrors(): void
    {
        $form = new UploadForm();
        $picture = self::createUploadedFile(UPLOAD_ERR_INI_SIZE);
        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', '/')
            ->withUploadedFiles(['UploadForm' => ['picture' => $picture]]);

        TestHelper::createFormHydrator()->populateFromPostWithFiles($form, $request);

        $this->assertSame($picture, $form->picture);
    }

    public function testPopulateFromPostWithFilesWithNestedFormAndCollection(): void
    {
        $form = new UploadForm();
        $avatar = self::createUploadedFile();
        $first = self::createUploadedFile();
        $third = self::createUploadedFile();
        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', '/')
            ->withParsedBody(['UploadForm' => ['profile' => ['name' => 'John']]])
            ->withUploadedFiles([
                'UploadForm' => [
                    'profile' => ['avatar' => $avatar],
                    'gallery' => [$first, self::createUploadedFile(UPLOAD_ERR_NO_FILE), $third],
                ],
            ]);

        TestHelper::createFormHydrator()->populateFromPostWithFiles($form, $request);

        $this->assertSame('John', $form->profile->name);
        $this->assertSame($avatar, $form->profile->avatar);
        $this->assertSame([0 => $first, 2 => $third], $form->gallery);
    }

    public function testPopulateFromPostWithFilesSkipsArraysWithoutUploadedFiles(): void
    {
        $form = new UploadForm();
        $form->gallery = ['previous'];
        $picture = self::createUploadedFile();
        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', '/')
            ->withParsedBody(['UploadForm' => ['title' => 'Holiday']])
            ->withUploadedFiles([
                'UploadForm' => [
                    'gallery' => [
                        self::createUploadedFile(UPLOAD_ERR_NO_FILE),
                        [self::createUploadedFile(UPLOAD_ERR_NO_FILE)],
                    ],
                    'picture' => $picture,
                ],
            ]);

        TestHelper::createFormHydrator()->populateFromPostWithFiles($form, $request);

        $this->assertSame(['previous'], $form->gallery);
        $this->assertSame($picture, $form->picture);
    }

    public function testPopulateFromPostWithFilesPrefersFilesOverBody(): void
    {
        $form = new UploadForm();
        $picture = self::createUploadedFile();
        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', '/')
            ->withParsedBody(['UploadForm' => ['title' => 'Holiday', 'picture' => 'fake']])
            ->withUploadedFiles(['UploadForm' => ['picture' => $picture]]);

        TestHelper::createFormHydrator()->populateFromPostWithFiles($form, $request);

        $this->assertSame('Holiday', $form->title);
        $this->assertSame($picture, $form->picture);
    }

    public function testPopulateFromPostWithFilesIgnoresBodyStringForNotUploadedFile(): void
    {
        $form = new UploadForm();
        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', '/')
            ->withParsedBody(['UploadForm' => ['title' => 'Holiday', 'picture' => '/etc/passwd']])
            ->withUploadedFiles(['UploadForm' => ['picture' => self::createUploadedFile(UPLOAD_ERR_NO_FILE)]]);

        TestHelper::createFormHydrator()->populateFromPostWithFiles($form, $request);

        $this->assertSame('Holiday', $form->title);
        $this->assertNull($form->picture);
    }

    public function testPopulateFromPostWithFilesAndValidateWithUploadError(): void
    {
        $form = new class extends FormModel {
            #[Required]
            #[Image]
            public ?UploadedFileInterface $picture = null;
        };
        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', '/')
            ->withUploadedFiles(['picture' => self::createUploadedFile(UPLOAD_ERR_INI_SIZE)]);

        $result = TestHelper::createFormHydrator()->populateFromPostWithFilesAndValidate($form, $request);

        $this->assertFalse($result);
        $this->assertInstanceOf(UploadedFileInterface::class, $form->picture);
        $this->assertSame(
            ['picture' => ['Picture must be an image.']],
            $form->getValidationResult()->getErrorMessagesIndexedByPath(),
        );
    }

    public static function dataPopulateFromPostWithFilesFails(): array
    {
        $factory = new ServerRequestFactory();
        $files = ['UploadForm' => ['picture' => self::createUploadedFile()]];

        return [
            'non-post' => [
                $factory
                    ->createServerRequest('GET', '/')
                    ->withParsedBody(['UploadForm' => ['title' => 'Holiday']])
                    ->withUploadedFiles($files),
            ],
            'empty-data' => [
                $factory->createServerRequest('POST', '/'),
            ],
            'not-uploaded-files-only' => [
                $factory
                    ->createServerRequest('POST', '/')
                    ->withUploadedFiles(['UploadForm' => ['picture' => self::createUploadedFile(UPLOAD_ERR_NO_FILE)]]),
            ],
            'object-body' => [
                $factory->createServerRequest('POST', '/')->withParsedBody(new stdClass()),
            ],
        ];
    }

    #[DataProvider('dataPopulateFromPostWithFilesFails')]
    public function testPopulateFromPostWithFilesFails(ServerRequestInterface $request): void
    {
        $form = new UploadForm();

        $result = TestHelper::createFormHydrator()->populateFromPostWithFiles($form, $request);

        $this->assertFalse($result);
        $this->assertSame('', $form->title);
        $this->assertNull($form->picture);
    }

    public function testPopulateFromPostWithFilesWithObjectBodyAndFiles(): void
    {
        $form = new UploadForm();
        $picture = self::createUploadedFile();
        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', '/')
            ->withParsedBody(new stdClass())
            ->withUploadedFiles(['UploadForm' => ['picture' => $picture]]);

        $result = TestHelper::createFormHydrator()->populateFromPostWithFiles($form, $request);

        $this->assertTrue($result);
        $this->assertSame($picture, $form->picture);
    }

    public static function dataPopulateFromPostWithFilesAndValidate(): array
    {
        $factory = new ServerRequestFactory();
        $body = ['UploadForm' => ['title' => 'Holiday', 'profile' => ['name' => 'John']]];

        return [
            'non-post' => [
                false,
                $factory
                    ->createServerRequest('GET', '/')
                    ->withParsedBody($body)
                    ->withUploadedFiles(['UploadForm' => ['picture' => self::createUploadedFile()]]),
            ],
            'empty-data' => [
                false,
                $factory->createServerRequest('POST', '/'),
            ],
            'not-uploaded-required-file' => [
                false,
                $factory
                    ->createServerRequest('POST', '/')
                    ->withParsedBody($body)
                    ->withUploadedFiles(['UploadForm' => ['picture' => self::createUploadedFile(UPLOAD_ERR_NO_FILE)]]),
            ],
            'valid-data' => [
                true,
                $factory
                    ->createServerRequest('POST', '/')
                    ->withParsedBody($body)
                    ->withUploadedFiles([
                        'UploadForm' => [
                            'picture' => self::createUploadedFile(),
                            'attachment' => self::createUploadedFile(UPLOAD_ERR_NO_FILE),
                        ],
                    ]),
            ],
        ];
    }

    #[DataProvider('dataPopulateFromPostWithFilesAndValidate')]
    public function testPopulateFromPostWithFilesAndValidate(bool $expected, ServerRequestInterface $request): void
    {
        $form = new UploadForm();

        $result = TestHelper::createFormHydrator()->populateFromPostWithFilesAndValidate($form, $request);

        $this->assertSame($expected, $result);
    }

    private static function createUploadedFile(int $error = UPLOAD_ERR_OK): UploadedFileInterface
    {
        return new UploadedFile('php://memory', 0, $error, 'image.png', 'image/png');
    }
}
