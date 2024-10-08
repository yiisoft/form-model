<?php

declare(strict_types=1);

namespace Yiisoft\FormModel\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Yiisoft\Form\Field\Email;
use Yiisoft\Form\Field\File;
use Yiisoft\Form\Field\Number;
use Yiisoft\Form\Field\Password;
use Yiisoft\Form\Field\Range;
use Yiisoft\Form\Field\Select;
use Yiisoft\Form\Field\Telephone;
use Yiisoft\Form\Field\Textarea;
use Yiisoft\Form\Theme\ThemeContainer;
use Yiisoft\FormModel\Field;
use Yiisoft\FormModel\FormModelInputData;
use Yiisoft\FormModel\Tests\Support\Form\DateForm;
use Yiisoft\FormModel\Tests\Support\Form\EmailForm;
use Yiisoft\FormModel\Tests\Support\Form\FileForm;
use Yiisoft\FormModel\Tests\Support\Form\NumberForm;
use Yiisoft\FormModel\Tests\Support\Form\PasswordForm;
use Yiisoft\FormModel\Tests\Support\Form\RangeForm;
use Yiisoft\FormModel\Tests\Support\Form\SelectForm;
use Yiisoft\FormModel\Tests\Support\Form\TelephoneForm;
use Yiisoft\FormModel\Tests\Support\Form\TextareaForm;
use Yiisoft\FormModel\Tests\Support\Form\TextForm;
use Yiisoft\FormModel\Tests\Support\Form\UrlForm;
use Yiisoft\FormModel\Tests\Support\StubDateTimeInputField;
use Yiisoft\FormModel\Tests\Support\StubField;
use Yiisoft\FormModel\ValidationRulesEnricher;
use Yiisoft\Validator\Rule\Required;

final class ValidationRulesEnricherTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        ThemeContainer::initialize(
            [
                'default' => [
                    'validationRulesEnricher' => new ValidationRulesEnricher(),
                ],
            ],
            'default',
        );
    }

    public static function dataUrl(): array
    {
        return [
            'required' => [
                '<input type="url" id="urlform-company" name="UrlForm[company]" value required>',
                'company',
            ],
            'required-with-when' => [
                '<input type="url" id="urlform-requiredwhen" name="UrlForm[requiredWhen]" minlength="7">',
                'requiredWhen',
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
                '<input type="url" id="urlform-beach" name="UrlForm[beach]" value pattern="^((?i)http|https):\/\/(([a-zA-Z0-9][a-zA-Z0-9_-]*)(\.[a-zA-Z0-9][a-zA-Z0-9_-]*)+)(?::\d{1,5})?([?\/#].*$|$)">',
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
            ->enrichFromValidationRules()
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
                '<input type="text" id="textform-requiredwhen" name="TextForm[requiredWhen]" minlength="7">',
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
            ->enrichFromValidationRules()
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
                '<textarea id="textareaform-requiredwhen" name="TextareaForm[requiredWhen]" minlength="7"></textarea>',
                'requiredWhen',
            ],
        ];
    }

    #[DataProvider('dataTextarea')]
    public function testTextarea(string $expected, string $property): void
    {
        $field = Textarea::widget()
            ->inputData(new FormModelInputData(new TextareaForm(), $property))
            ->hideLabel()
            ->useContainer(false)
            ->enrichFromValidationRules();

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
                '<input type="tel" id="telephoneform-requiredwhen" name="TelephoneForm[requiredWhen]" minlength="7">',
                'requiredWhen',
            ],
        ];
    }

    #[DataProvider('dataTelephone')]
    public function testTelephone(string $expected, string $property): void
    {
        $field = Telephone::widget()
            ->inputData(new FormModelInputData(new TelephoneForm(), $property))
            ->hideLabel()
            ->enrichFromValidationRules()
            ->useContainer(false);

        $this->assertSame($expected, $field->render());
    }

    public function testSelect(): void
    {
        $result = Select::widget()
            ->inputData(new FormModelInputData(new SelectForm(), 'color'))
            ->optionsData(['red' => 'Red'])
            ->enrichFromValidationRules()
            ->hideLabel()
            ->useContainer(false)
            ->render();

        $expected = <<<HTML
            <select id="selectform-color" name="SelectForm[color]" required>
            <option value="red">Red</option>
            </select>
            HTML;

        $this->assertSame($expected, $result);
    }

    public function testSelectWithWhen(): void
    {
        $result = Select::widget()
            ->inputData(new FormModelInputData(new SelectForm(), 'requiredWhen'))
            ->optionsData(['red' => 'Red'])
            ->enrichFromValidationRules()
            ->hideLabel()
            ->useContainer(false)
            ->render();

        $expected = <<<HTML
            <select id="selectform-requiredwhen" name="SelectForm[requiredWhen]">
            <option value="red">Red</option>
            </select>
            HTML;

        $this->assertSame($expected, $result);
    }

    public function testSelectWithWhenNext(): void
    {
        $result = Select::widget()
            ->inputData(new FormModelInputData(new SelectForm(), 'requiredWhenNext'))
            ->optionsData(['red' => 'Red'])
            ->enrichFromValidationRules()
            ->hideLabel()
            ->useContainer(false)
            ->render();

        $expected = <<<HTML
            <select id="selectform-requiredwhennext" name="SelectForm[requiredWhenNext]" required>
            <option value="red">Red</option>
            </select>
            HTML;

        $this->assertSame($expected, $result);
    }

    public static function dataPassword(): array
    {
        return [
            'required' => [
                '<input type="password" id="passwordform-entry1" name="PasswordForm[entry1]" required>',
                'entry1',
            ],
            'has-length' => [
                '<input type="password" id="passwordform-entry2" name="PasswordForm[entry2]" maxlength="199" minlength="10">',
                'entry2',
            ],
            'regex' => [
                '<input type="password" id="passwordform-code" name="PasswordForm[code]" pattern="\w+">',
                'code',
            ],
            'regex-not' => [
                '<input type="password" id="passwordform-nocode" name="PasswordForm[nocode]">',
                'nocode',
            ],
            'required-with-when' => [
                '<input type="password" id="passwordform-requiredwhen" name="PasswordForm[requiredWhen]" minlength="7">',
                'requiredWhen',
            ],
        ];
    }

    #[DataProvider('dataPassword')]
    public function testPassword(string $expected, string $property): void
    {
        $field = Password::widget()
            ->inputData(new FormModelInputData(new PasswordForm(), $property))
            ->hideLabel()
            ->enrichFromValidationRules()
            ->useContainer(false);

        $this->assertSame($expected, $field->render());
    }

    public function testFile(): void
    {
        $result = File::widget()
            ->inputData(new FormModelInputData(new FileForm(), 'image'))
            ->hideLabel()
            ->enrichFromValidationRules()
            ->render();

        $expected = <<<HTML
            <div>
            <input type="file" id="fileform-image" name="FileForm[image]" required>
            </div>
            HTML;

        $this->assertSame($expected, $result);
    }

    public function testFileWithWhen(): void
    {
        $result = File::widget()
            ->inputData(new FormModelInputData(new FileForm(), 'photo'))
            ->hideLabel()
            ->enrichFromValidationRules()
            ->render();

        $expected = <<<HTML
            <div>
            <input type="file" id="fileform-photo" name="FileForm[photo]">
            </div>
            HTML;

        $this->assertSame($expected, $result);
    }

    public function testFileWithWhenNext(): void
    {
        $result = File::widget()
            ->inputData(new FormModelInputData(new FileForm(), 'video'))
            ->hideLabel()
            ->enrichFromValidationRules()
            ->render();

        $expected = <<<HTML
            <div>
            <input type="file" id="fileform-video" name="FileForm[video]" required>
            </div>
            HTML;

        $this->assertSame($expected, $result);
    }

    public function testDateTimeInputField(): void
    {
        $result = StubDateTimeInputField::widget()
            ->inputData(new FormModelInputData(new DateForm(), 'main'))
            ->hideLabel()
            ->enrichFromValidationRules()
            ->render();

        $expected = <<<HTML
            <div>
            <input type="datetime" id="dateform-main" name="DateForm[main]" required>
            </div>
            HTML;

        $this->assertSame($expected, $result);
    }

    public function testDateTimeInputFieldWithWhen(): void
    {
        $result = StubDateTimeInputField::widget()
            ->inputData(new FormModelInputData(new DateForm(), 'second'))
            ->hideLabel()
            ->enrichFromValidationRules()
            ->render();

        $expected = <<<HTML
            <div>
            <input type="datetime" id="dateform-second" name="DateForm[second]">
            </div>
            HTML;

        $this->assertSame($expected, $result);
    }

    public function testDateTimeInputFieldWithWhenAndNext(): void
    {
        $result = StubDateTimeInputField::widget()
            ->inputData(new FormModelInputData(new DateForm(), 'three'))
            ->hideLabel()
            ->enrichFromValidationRules()
            ->render();

        $expected = <<<HTML
            <div>
            <input type="datetime" id="dateform-three" name="DateForm[three]" required>
            </div>
            HTML;

        $this->assertSame($expected, $result);
    }

    public static function dataEmail(): array
    {
        return [
            'required' => [
                '<input type="email" id="emailform-cto" name="EmailForm[cto]" required>',
                'cto',
            ],
            'has-length' => [
                '<input type="email" id="emailform-teamlead" name="EmailForm[teamlead]" maxlength="199" minlength="10">',
                'teamlead',
            ],
            'regex' => [
                '<input type="email" id="emailform-code" name="EmailForm[code]" pattern="\w+@\w+">',
                'code',
            ],
            'regex-not' => [
                '<input type="email" id="emailform-nocode" name="EmailForm[nocode]">',
                'nocode',
            ],
            'required-with-when' => [
                '<input type="email" id="emailform-requiredwhen" name="EmailForm[requiredWhen]" minlength="7">',
                'requiredWhen',
            ],
        ];
    }

    #[DataProvider('dataEmail')]
    public function testEmail(string $expected, string $attribute): void
    {
        $field = Email::widget()
            ->inputData(new FormModelInputData(new EmailForm(), $attribute))
            ->hideLabel()
            ->enrichFromValidationRules()
            ->useContainer(false);

        $this->assertSame($expected, $field->render());
    }

    public static function dataNumber(): array
    {
        return [
            'required' => [
                '<input type="number" id="numberform-weight" name="NumberForm[weight]" required>',
                'weight',
            ],
            'number' => [
                '<input type="number" id="numberform-step" name="NumberForm[step]" min="5" max="95">',
                'step',
            ],
            'required-with-when' => [
                '<input type="number" id="numberform-requiredwhen" name="NumberForm[requiredWhen]" min="5">',
                'requiredWhen',
            ],
        ];
    }

    #[DataProvider('dataNumber')]
    public function testNumber(string $expected, string $property): void
    {
        $field = Number::widget()
            ->inputData(new FormModelInputData(new NumberForm(), $property))
            ->hideLabel()
            ->enrichFromValidationRules()
            ->useContainer(false);

        $this->assertSame($expected, $field->render());
    }

    public static function dataRange(): array
    {
        return [
            'required' => [
                '<input type="range" id="rangeform-volume" name="RangeForm[volume]" value="23" required>',
                'volume',
            ],
            'number' => [
                '<input type="range" id="rangeform-count" name="RangeForm[count]" min="1" max="9">',
                'count',
            ],
            'required-with-when' => [
                '<input type="range" id="rangeform-requiredwhen" name="RangeForm[requiredWhen]" min="1">',
                'requiredWhen',
            ],
        ];
    }

    #[DataProvider('dataRange')]
    public function testRange(string $expected, string $property): void
    {
        $field = Range::widget()
            ->inputData(new FormModelInputData(new RangeForm(), $property))
            ->hideLabel()
            ->useContainer(false)
            ->enrichFromValidationRules();

        $this->assertSame($expected, $field->render());
    }

    public function testNonIterableRules(): void
    {
        $field = Range::widget();
        $enricher = new ValidationRulesEnricher();

        $this->assertNull(
            $enricher->process($field, new Required())
        );
    }

    public function testNotSupportedWidget(): void
    {
        $field = StubField::widget();
        $enricher = new ValidationRulesEnricher();

        $this->assertNull(
            $enricher->process($field, [])
        );
    }
}
