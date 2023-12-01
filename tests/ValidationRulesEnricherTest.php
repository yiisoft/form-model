<?php

declare(strict_types=1);

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Yiisoft\Form\ThemeContainer;
use Yiisoft\FormModel\Field;
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

    public static function dataEnrichFromValidationRules(): array
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

    #[DataProvider('dataEnrichFromValidationRules')]
    public function testEnrichFromValidationRules(string $expected, string $property): void
    {
        $field = Field::url(new UrlForm(), $property)
            ->hideLabel()
            ->enrichFromValidationRules(true)
            ->useContainer(false);

        $this->assertSame($expected, $field->render());
    }
}
