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
                '<input type="url" name="UrlForm[company]" value required id="urlform-company">',
                'company',
            ],
            'required-with-when' => [
                '<input type="url" name="UrlForm[requiredWhen]" minlength="7" id="urlform-requiredwhen">',
                'requiredWhen',
            ],
            'has-length' => [
                '<input type="url" name="UrlForm[home]" value minlength="50" maxlength="199" id="urlform-home">',
                'home',
            ],
            'regex' => [
                '<input type="url" name="UrlForm[code]" value pattern="\w+" id="urlform-code">',
                'code',
            ],
            'regex-not' => [
                '<input type="url" name="UrlForm[nocode]" value id="urlform-nocode">',
                'nocode',
            ],
            'url' => [
                '<input type="url" name="UrlForm[shop]" value pattern="^((?i)http|https):\/\/(([a-zA-Z0-9][a-zA-Z0-9_-]*)(\.[a-zA-Z0-9][a-zA-Z0-9_-]*)+)(?::\d{1,5})?([?\/#].*$|$)" id="urlform-shop">',
                'shop',
            ],
            'url-regex' => [
                '<input type="url" name="UrlForm[beach]" value pattern="^((?i)http|https):\/\/(([a-zA-Z0-9][a-zA-Z0-9_-]*)(\.[a-zA-Z0-9][a-zA-Z0-9_-]*)+)(?::\d{1,5})?([?\/#].*$|$)" id="urlform-beach">',
                'beach',
            ],
            'regex-url' => [
                '<input type="url" name="UrlForm[beach2]" value pattern="^((?i)http|https):\/\/(([a-zA-Z0-9][a-zA-Z0-9_-]*)(\.[a-zA-Z0-9][a-zA-Z0-9_-]*)+)(?::\d{1,5})?([?\/#].*$|$)" id="urlform-beach2">',
                'beach2',
            ],
            'url-with-idn' => [
                '<input type="url" name="UrlForm[urlWithIdn]" value id="urlform-urlwithidn">',
                'urlWithIdn',
            ],
            'regex-and-url-with-idn' => [
                '<input type="url" name="UrlForm[regexAndUrlWithIdn]" value pattern="\w+" id="urlform-regexandurlwithidn">',
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
                '<input type="text" name="TextForm[company]" value required id="textform-company">',
                'company',
            ],
            'has-length' => [
                '<input type="text" name="TextForm[shortdesc]" value minlength="10" maxlength="199" id="textform-shortdesc">',
                'shortdesc',
            ],
            'regex' => [
                '<input type="text" name="TextForm[code]" value pattern="\w+" id="textform-code">',
                'code',
            ],
            'regex-not' => [
                '<input type="text" name="TextForm[nocode]" value id="textform-nocode">',
                'nocode',
            ],
            'required-with-when' => [
                '<input type="text" name="TextForm[requiredWhen]" minlength="7" id="textform-requiredwhen">',
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
                '<textarea name="TextareaForm[bio]" required id="textareaform-bio"></textarea>',
                'bio',
            ],
            'has-length' => [
                '<textarea name="TextareaForm[shortdesc]" minlength="10" maxlength="199" id="textareaform-shortdesc"></textarea>',
                'shortdesc',
            ],
            'required-with-when' => [
                '<textarea name="TextareaForm[requiredWhen]" minlength="7" id="textareaform-requiredwhen"></textarea>',
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
                '<input type="tel" name="TelephoneForm[office1]" required id="telephoneform-office1">',
                'office1',
            ],
            'has-length' => [
                '<input type="tel" name="TelephoneForm[office2]" minlength="10" maxlength="199" id="telephoneform-office2">',
                'office2',
            ],
            'regex' => [
                '<input type="tel" name="TelephoneForm[code]" pattern="\w+" id="telephoneform-code">',
                'code',
            ],
            'regex-not' => [
                '<input type="tel" name="TelephoneForm[nocode]" id="telephoneform-nocode">',
                'nocode',
            ],
            'required-with-when' => [
                '<input type="tel" name="TelephoneForm[requiredWhen]" minlength="7" id="telephoneform-requiredwhen">',
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
            <select required id="selectform-color" name="SelectForm[color]">
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
            <select required id="selectform-requiredwhennext" name="SelectForm[requiredWhenNext]">
            <option value="red">Red</option>
            </select>
            HTML;

        $this->assertSame($expected, $result);
    }

    public static function dataPassword(): array
    {
        return [
            'required' => [
                '<input type="password" name="PasswordForm[entry1]" required id="passwordform-entry1">',
                'entry1',
            ],
            'has-length' => [
                '<input type="password" name="PasswordForm[entry2]" minlength="10" maxlength="199" id="passwordform-entry2">',
                'entry2',
            ],
            'regex' => [
                '<input type="password" name="PasswordForm[code]" pattern="\w+" id="passwordform-code">',
                'code',
            ],
            'regex-not' => [
                '<input type="password" name="PasswordForm[nocode]" id="passwordform-nocode">',
                'nocode',
            ],
            'required-with-when' => [
                '<input type="password" name="PasswordForm[requiredWhen]" minlength="7" id="passwordform-requiredwhen">',
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
            <input name="FileForm[image]" required id="fileform-image" type="file">
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
            <input name="FileForm[photo]" id="fileform-photo" type="file">
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
            <input name="FileForm[video]" required id="fileform-video" type="file">
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
            <input type="datetime" name="DateForm[main]" required id="dateform-main">
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
            <input type="datetime" name="DateForm[second]" id="dateform-second">
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
            <input type="datetime" name="DateForm[three]" required id="dateform-three">
            </div>
            HTML;

        $this->assertSame($expected, $result);
    }

    public static function dataEmail(): array
    {
        return [
            'required' => [
                '<input type="email" name="EmailForm[cto]" required id="emailform-cto">',
                'cto',
            ],
            'has-length' => [
                '<input type="email" name="EmailForm[teamlead]" minlength="10" maxlength="199" id="emailform-teamlead">',
                'teamlead',
            ],
            'regex' => [
                '<input type="email" name="EmailForm[code]" pattern="\w+@\w+" id="emailform-code">',
                'code',
            ],
            'regex-not' => [
                '<input type="email" name="EmailForm[nocode]" id="emailform-nocode">',
                'nocode',
            ],
            'required-with-when' => [
                '<input type="email" name="EmailForm[requiredWhen]" minlength="7" id="emailform-requiredwhen">',
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
                '<input type="number" name="NumberForm[weight]" required id="numberform-weight">',
                'weight',
            ],
            'number' => [
                '<input type="number" name="NumberForm[step]" min="5" max="95" id="numberform-step">',
                'step',
            ],
            'required-with-when' => [
                '<input type="number" name="NumberForm[requiredWhen]" min="5" id="numberform-requiredwhen">',
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
                '<input name="RangeForm[volume]" value="23" required id="rangeform-volume" type="range">',
                'volume',
            ],
            'number' => [
                '<input name="RangeForm[count]" min="1" max="9" id="rangeform-count" type="range">',
                'count',
            ],
            'required-with-when' => [
                '<input name="RangeForm[requiredWhen]" min="1" id="rangeform-requiredwhen" type="range">',
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
            $enricher->process($field, new Required()),
        );
    }

    public function testNotSupportedWidget(): void
    {
        $field = StubField::widget();
        $enricher = new ValidationRulesEnricher();

        $this->assertNull(
            $enricher->process($field, []),
        );
    }
}
