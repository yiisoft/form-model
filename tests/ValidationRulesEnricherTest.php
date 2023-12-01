<?php

declare(strict_types=1);

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Yiisoft\Form\Field\Telephone;
use Yiisoft\Form\Field\Textarea;
use Yiisoft\Form\ThemeContainer;
use Yiisoft\FormModel\Field;
use Yiisoft\FormModel\FormModelInputData;
use Yiisoft\FormModel\Tests\Support\Form\TelephoneForm;
use Yiisoft\FormModel\Tests\Support\Form\TextareaForm;
use Yiisoft\FormModel\Tests\Support\Form\TextForm;
use Yiisoft\FormModel\Tests\Support\Form\UrlForm;
use Yiisoft\FormModel\ValidationRulesEnricher;
use Yiisoft\Test\Support\Container\SimpleContainer;
use Yiisoft\Widget\WidgetFactory;

final class ValidationRulesEnricherTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        WidgetFactory::initialize(new SimpleContainer());
        ThemeContainer::initialize(
            validationRulesEnricher: new ValidationRulesEnricher()
        );
    }

    public static function dataUrl(): array
    {
        return [
            'required' => [
                '<input type="url" id="urlform-company" name="UrlForm[company]" value required>',
                'company',
            ],
            'has-length' => [
                '<input type="url" id="urlform-home" name="UrlForm[home]" value maxlength="199" minlength="50">',
                'home',
            ],
            'regex' => [
                '<input type="url" id="urlform-code" name="UrlForm[code]" value pattern="\w+">',
                'code',
            ],
            'regex-not' => [
                '<input type="url" id="urlform-nocode" name="UrlForm[nocode]" value>',
                'nocode',
            ],
            'url' => [
                '<input type="url" id="urlform-shop" name="UrlForm[shop]" value pattern="^((?i)http|https):\/\/(([a-zA-Z0-9][a-zA-Z0-9_-]*)(\.[a-zA-Z0-9][a-zA-Z0-9_-]*)+)(?::\d{1,5})?([?\/#].*$|$)">',
                'shop',
            ],
            'url-regex' => [
                '<input type="url" id="urlform-beach" name="UrlForm[beach]" value pattern="\w+">',
                'beach',
            ],
            'regex-url' => [
                '<input type="url" id="urlform-beach2" name="UrlForm[beach2]" value pattern="^((?i)http|https):\/\/(([a-zA-Z0-9][a-zA-Z0-9_-]*)(\.[a-zA-Z0-9][a-zA-Z0-9_-]*)+)(?::\d{1,5})?([?\/#].*$|$)">',
                'beach2',
            ],
            'url-with-idn' => [
                '<input type="url" id="urlform-urlwithidn" name="UrlForm[urlWithIdn]" value>',
                'urlWithIdn',
            ],
            'regex-and-url-with-idn' => [
                '<input type="url" id="urlform-regexandurlwithidn" name="UrlForm[regexAndUrlWithIdn]" value pattern="\w+">',
                'regexAndUrlWithIdn',
            ],
        ];
    }

    #[DataProvider('dataUrl')]
    public function testUrl(string $expected, string $property): void
    {
        $field = Field::url(new UrlForm(), $property)
            ->hideLabel()
            ->enrichFromValidationRules(true)
            ->useContainer(false);

        $this->assertSame($expected, $field->render());
    }

    public static function dataText(): array
    {
        return [
            'required' => [
                '<input type="text" id="textform-company" name="TextForm[company]" value required>',
                'company',
            ],
            'has-length' => [
                '<input type="text" id="textform-shortdesc" name="TextForm[shortdesc]" value maxlength="199" minlength="10">',
                'shortdesc',
            ],
            'regex' => [
                '<input type="text" id="textform-code" name="TextForm[code]" value pattern="\w+">',
                'code',
            ],
            'regex-not' => [
                '<input type="text" id="textform-nocode" name="TextForm[nocode]" value>',
                'nocode',
            ],
            'required-with-when' => [
                '<input type="text" id="textform-requiredwhen" name="TextForm[requiredWhen]">',
                'requiredWhen',
            ],
        ];
    }

    /**
     * @dataProvider dataText
     */
    public function testText(string $expected, string $property): void
    {
        $field = Field::text(new TextForm(), $property)
            ->hideLabel()
            ->enrichFromValidationRules(true)
            ->useContainer(false);

        $this->assertSame($expected, $field->render());
    }

    public static function dataTextarea(): array
    {
        return [
            'required' => [
                '<textarea id="textareaform-bio" name="TextareaForm[bio]" required></textarea>',
                'bio',
            ],
            'has-length' => [
                '<textarea id="textareaform-shortdesc" name="TextareaForm[shortdesc]" maxlength="199" minlength="10"></textarea>',
                'shortdesc',
            ],
            'required-with-when' => [
                '<textarea id="textareaform-requiredwhen" name="TextareaForm[requiredWhen]"></textarea>',
                'requiredWhen',
            ],
        ];
    }

    #[DataProvider('dataTextarea')]
    public function testTextarea(string $expected, string $attribute): void
    {
        $field = Textarea::widget()
            ->inputData(new FormModelInputData(new TextareaForm(), $attribute))
            ->hideLabel()
            ->useContainer(false)
            ->enrichFromValidationRules(true);

        $this->assertSame($expected, $field->render());
    }

    public static function dataTelephone(): array
    {
        return [
            'required' => [
                '<input type="tel" id="telephoneform-office1" name="TelephoneForm[office1]" required>',
                'office1',
            ],
            'has-length' => [
                '<input type="tel" id="telephoneform-office2" name="TelephoneForm[office2]" maxlength="199" minlength="10">',
                'office2',
            ],
            'regex' => [
                '<input type="tel" id="telephoneform-code" name="TelephoneForm[code]" pattern="\w+">',
                'code',
            ],
            'regex-not' => [
                '<input type="tel" id="telephoneform-nocode" name="TelephoneForm[nocode]">',
                'nocode',
            ],
            'required-with-when' => [
                '<input type="tel" id="telephoneform-requiredwhen" name="TelephoneForm[requiredWhen]">',
                'requiredWhen',
            ],
        ];
    }

    #[DataProvider('dataTelephone')]
    public function testTelephone(string $expected, string $attribute): void
    {
        $field = Telephone::widget()
            ->inputData(new FormModelInputData(new TelephoneForm(), $attribute))
            ->hideLabel()
            ->enrichFromValidationRules(true)
            ->useContainer(false);

        $this->assertSame($expected, $field->render());
    }
}
