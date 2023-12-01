<?php

declare(strict_types=1);

namespace Yiisoft\FormModel\Tests\Field;

use PHPUnit\Framework\TestCase;
use Yiisoft\Form\ThemeContainer;
use Yiisoft\FormModel\Field\ErrorSummary;
use Yiisoft\FormModel\Tests\Support\Form\ErrorSummaryForm;
use Yiisoft\Test\Support\Container\SimpleContainer;
use Yiisoft\Validator\Result;
use Yiisoft\Widget\WidgetFactory;

final class ErrorSummaryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        WidgetFactory::initialize(new SimpleContainer());
        ThemeContainer::initialize();
    }

    public function testBase(): void
    {
        $result = ErrorSummary::widget()
            ->validationResult(ErrorSummaryForm::validated()->getValidationResult())
            ->render();

        $expected = <<<HTML
            <div>
            <p>Please fix the following errors:</p>
            <ul>
            <li>Value cannot be blank.</li>
            <li>&lt;b&gt;Very&lt;/b&gt; old.</li>
            <li>Bad year.</li>
            </ul>
            </div>
            HTML;

        $this->assertSame($expected, $result);
    }

    public function testNonValidateForm(): void
    {
        $result = ErrorSummary::widget()
            ->validationResult((new ErrorSummaryForm())->getValidationResult())
            ->render();

        $this->assertSame('', $result);
    }

    public function testNoEncode(): void
    {
        $result = ErrorSummary::widget()
            ->validationResult(ErrorSummaryForm::validated()->getValidationResult())
            ->onlyProperties('age')
            ->encode(false)
            ->render();

        $expected = <<<HTML
            <div>
            <p>Please fix the following errors:</p>
            <ul>
            <li><b>Very</b> old.</li>
            </ul>
            </div>
            HTML;

        $this->assertSame($expected, $result);
    }

    public function testShowAllErrors(): void
    {
        $result = ErrorSummary::widget()
            ->validationResult(ErrorSummaryForm::validated()->getValidationResult())
            ->onlyProperties('year')
            ->showAllErrors()
            ->render();

        $expected = <<<HTML
            <div>
            <p>Please fix the following errors:</p>
            <ul>
            <li>Bad year.</li>
            <li>Very Bad year.</li>
            </ul>
            </div>
            HTML;

        $this->assertSame($expected, $result);
    }

    public function testOnlyCommonErrors(): void
    {
        $form = ErrorSummaryForm::validated();
        $form->getValidationResult()
            ->addError('Common error 1')
            ->addError('Common error 2');

        $result = ErrorSummary::widget()
            ->validationResult($form->getValidationResult())
            ->onlyCommonErrors()
            ->showAllErrors()
            ->render();

        $expected = <<<HTML
            <div>
            <p>Please fix the following errors:</p>
            <ul>
            <li>Common error 1</li>
            <li>Common error 2</li>
            </ul>
            </div>
            HTML;

        $this->assertSame($expected, $result);
    }

    public function testFooter(): void
    {
        $result = ErrorSummary::widget()
            ->validationResult(ErrorSummaryForm::validated()->getValidationResult())
            ->onlyProperties('year')
            ->footer('Footer text.')
            ->footerAttributes(['class' => 'footer'])
            ->render();

        $expected = <<<HTML
            <div>
            <p>Please fix the following errors:</p>
            <ul>
            <li>Bad year.</li>
            </ul>
            <p class="footer">Footer text.</p>
            </div>
            HTML;

        $this->assertSame($expected, $result);
    }

    public function testHeader(): void
    {
        $result = ErrorSummary::widget()
            ->validationResult(ErrorSummaryForm::validated()->getValidationResult())
            ->onlyProperties('year')
            ->header('Header text.')
            ->headerAttributes(['class' => 'header'])
            ->render();

        $expected = <<<HTML
            <div>
            <p class="header">Header text.</p>
            <ul>
            <li>Bad year.</li>
            </ul>
            </div>
            HTML;

        $this->assertSame($expected, $result);
    }

    public function testListAttributes(): void
    {
        $result = ErrorSummary::widget()
            ->validationResult(ErrorSummaryForm::validated()->getValidationResult())
            ->onlyProperties('year')
            ->listAttributes(['class' => 'errorsList'])
            ->render();

        $expected = <<<HTML
            <div>
            <p>Please fix the following errors:</p>
            <ul class="errorsList">
            <li>Bad year.</li>
            </ul>
            </div>
            HTML;

        $this->assertSame($expected, $result);
    }

    public function testListClass(): void
    {
        $result = ErrorSummary::widget()
            ->validationResult(ErrorSummaryForm::validated()->getValidationResult())
            ->onlyProperties('year')
            ->listAttributes(['class' => 'list'])
            ->listClass('errorsList')
            ->render();

        $expected = <<<HTML
            <div>
            <p>Please fix the following errors:</p>
            <ul class="errorsList">
            <li>Bad year.</li>
            </ul>
            </div>
            HTML;

        $this->assertSame($expected, $result);
    }

    public function testAddListClass(): void
    {
        $result = ErrorSummary::widget()
            ->validationResult(ErrorSummaryForm::validated()->getValidationResult())
            ->onlyProperties('year')
            ->listClass('errorsList')
            ->addListClass('errorsList-tiny')
            ->render();

        $expected = <<<HTML
            <div>
            <p>Please fix the following errors:</p>
            <ul class="errorsList errorsList-tiny">
            <li>Bad year.</li>
            </ul>
            </div>
            HTML;

        $this->assertSame($expected, $result);
    }

    public function testWithoutData(): void
    {
        $result = ErrorSummary::widget()->render();
        $this->assertSame('', $result);
    }

    public function testImmutability(): void
    {
        $field = ErrorSummary::widget();

        $this->assertNotSame($field, $field->validationResult(new Result()));
        $this->assertNotSame($field, $field->encode(false));
        $this->assertNotSame($field, $field->showAllErrors());
        $this->assertNotSame($field, $field->onlyProperties());
        $this->assertNotSame($field, $field->onlyCommonErrors());
        $this->assertNotSame($field, $field->header(''));
        $this->assertNotSame($field, $field->headerAttributes([]));
        $this->assertNotSame($field, $field->listAttributes([]));
        $this->assertNotSame($field, $field->listClass());
        $this->assertNotSame($field, $field->addListClass());
        $this->assertNotSame($field, $field->footer(''));
        $this->assertNotSame($field, $field->footerAttributes([]));
    }
}
