<?php

declare(strict_types=1);

namespace Yiisoft\FormModel\Tests;

use PHPUnit\Framework\TestCase;
use Yiisoft\Form\Theme\ThemeContainer;
use Yiisoft\FormModel\Field;
use Yiisoft\FormModel\Tests\Support\FieldWithTheme;
use Yiisoft\FormModel\Tests\Support\Form\TestForm;
use Yiisoft\Html\Html;

final class FieldTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        ThemeContainer::initialize();
    }

    public function testButton(): void
    {
        $result = Field::button('Show info')->render();
        $this->assertSame(
            <<<HTML
            <div>
            <button type="button">Show info</button>
            </div>
            HTML,
            $result
        );
    }

    public function testButtonWithTheme(): void
    {
        ThemeContainer::initialize([
            'A' => [
                'containerClass' => 'red',
            ],
            'B' => [
                'containerClass' => 'green',
            ],
        ]);

        $result = FieldWithTheme::button('Show info', theme: 'B')->render();
        $this->assertSame(
            <<<HTML
            <div class="green">
            <button type="button">Show info</button>
            </div>
            HTML,
            $result
        );
    }

    public function testButtonGroup(): void
    {
        $result = Field::buttonGroup()
            ->buttons(
                Html::resetButton('Reset Data'),
                Html::submitButton('Send'),
            )
            ->render();

        $this->assertSame(
            <<<HTML
            <div>
            <button type="reset">Reset Data</button>
            <button type="submit">Send</button>
            </div>
            HTML,
            $result
        );
    }

    public function testButtonGroupWithTheme(): void
    {
        ThemeContainer::initialize([
            'A' => [
                'containerClass' => 'red',
            ],
            'B' => [
                'containerClass' => 'green',
            ],
        ]);

        $result = FieldWithTheme::buttonGroup(theme: 'B')
            ->buttons(
                Html::resetButton('Reset Data'),
                Html::submitButton('Send'),
            )
            ->render();

        $this->assertSame(
            <<<HTML
            <div class="green">
            <button type="reset">Reset Data</button>
            <button type="submit">Send</button>
            </div>
            HTML,
            $result
        );
    }

    public function testCheckbox(): void
    {
        $result = Field::checkbox(new TestForm(), 'blue')->render();
        $this->assertSame(
            <<<HTML
            <div>
            <input type="hidden" name="TestForm[blue]" value="0"><label><input type="checkbox" id="testform-blue" name="TestForm[blue]" value="1"> Blue color</label>
            </div>
            HTML,
            $result
        );
    }

    public function testCheckboxWithTheme(): void
    {
        ThemeContainer::initialize([
            'A' => [
                'containerClass' => 'red',
            ],
            'B' => [
                'containerClass' => 'green',
            ],
        ]);

        $result = FieldWithTheme::checkbox(new TestForm(), 'blue', theme: 'B')->render();
        $this->assertSame(
            <<<HTML
            <div class="green">
            <input type="hidden" name="TestForm[blue]" value="0"><label><input type="checkbox" id="testform-blue" name="TestForm[blue]" value="1"> Blue color</label>
            </div>
            HTML,
            $result
        );
    }

    public function testCheckboxList(): void
    {
        $result = Field::checkboxList(new TestForm(), 'color2')
            ->items([
                'red' => 'Red',
                'blue' => 'Blue',
            ])
            ->render();

        $expected = <<<'HTML'
        <div>
        <label>Select one or more colors</label>
        <div>
        <label><input type="checkbox" name="TestForm[color2][]" value="red"> Red</label>
        <label><input type="checkbox" name="TestForm[color2][]" value="blue"> Blue</label>
        </div>
        </div>
        HTML;

        $this->assertSame($expected, $result);
    }

    public function testCheckboxListWithTheme(): void
    {
        ThemeContainer::initialize([
            'A' => [
                'containerClass' => 'red',
            ],
            'B' => [
                'containerClass' => 'green',
            ],
        ]);

        $result = FieldWithTheme::checkboxList(new TestForm(), 'color2', theme: 'B')
            ->items([
                'red' => 'Red',
                'blue' => 'Blue',
            ])
            ->render();

        $expected = <<<'HTML'
        <div class="green">
        <label>Select one or more colors</label>
        <div>
        <label><input type="checkbox" name="TestForm[color2][]" value="red"> Red</label>
        <label><input type="checkbox" name="TestForm[color2][]" value="blue"> Blue</label>
        </div>
        </div>
        HTML;

        $this->assertSame($expected, $result);
    }

    public function testDate(): void
    {
        $result = Field::date(new TestForm(), 'birthday')->render();
        $this->assertSame(
            <<<HTML
            <div>
            <label for="testform-birthday">Birthday</label>
            <input type="date" id="testform-birthday" name="TestForm[birthday]" value="1996-12-19">
            </div>
            HTML,
            $result
        );
    }

    public function testDateWithTheme(): void
    {
        ThemeContainer::initialize([
            'A' => [
                'containerClass' => 'red',
            ],
            'B' => [
                'containerClass' => 'green',
            ],
        ]);

        $result = FieldWithTheme::date(new TestForm(), 'birthday', theme: 'B')->render();
        $this->assertSame(
            <<<HTML
            <div class="green">
            <label for="testform-birthday">Birthday</label>
            <input type="date" id="testform-birthday" name="TestForm[birthday]" value="1996-12-19">
            </div>
            HTML,
            $result
        );
    }

    public function testDateTimeLocal(): void
    {
        $result = Field::dateTimeLocal(new TestForm(), 'partyDate')->render();
        $this->assertSame(
            <<<HTML
            <div>
            <label for="testform-partydate">Date of party</label>
            <input type="datetime-local" id="testform-partydate" name="TestForm[partyDate]" value="2017-06-01T08:30">
            </div>
            HTML,
            $result
        );
    }

    public function testDateTimeLocalWithTheme(): void
    {
        ThemeContainer::initialize([
            'A' => [
                'containerClass' => 'red',
            ],
            'B' => [
                'containerClass' => 'green',
            ],
        ]);

        $result = FieldWithTheme::dateTimeLocal(new TestForm(), 'partyDate', theme: 'B')->render();
        $this->assertSame(
            <<<HTML
            <div class="green">
            <label for="testform-partydate">Date of party</label>
            <input type="datetime-local" id="testform-partydate" name="TestForm[partyDate]" value="2017-06-01T08:30">
            </div>
            HTML,
            $result
        );
    }

    public function testEmail(): void
    {
        $result = Field::email(new TestForm(), 'mainEmail')->render();
        $this->assertSame(
            <<<HTML
            <div>
            <label for="testform-mainemail">Main email</label>
            <input type="email" id="testform-mainemail" name="TestForm[mainEmail]" value>
            <div>Email for notifications.</div>
            </div>
            HTML,
            $result
        );
    }

    public function testEmailWithTheme(): void
    {
        ThemeContainer::initialize([
            'A' => [
                'containerClass' => 'red',
            ],
            'B' => [
                'containerClass' => 'green',
            ],
        ]);

        $result = FieldWithTheme::email(new TestForm(), 'mainEmail', theme: 'B')->render();
        $this->assertSame(
            <<<HTML
            <div class="green">
            <label for="testform-mainemail">Main email</label>
            <input type="email" id="testform-mainemail" name="TestForm[mainEmail]" value>
            <div>Email for notifications.</div>
            </div>
            HTML,
            $result
        );
    }

    public function testErrorSummary(): void
    {
        $result = Field::errorSummary(TestForm::validated())
            ->onlyProperties('name')
            ->render();

        $expected = <<<HTML
            <div>
            <ul>
            <li>Value cannot be blank.</li>
            <li>This value must contain at least 4 characters.</li>
            </ul>
            </div>
            HTML;

        $this->assertSame($expected, $result);
    }

    public function testErrorSummaryWithTheme(): void
    {
        ThemeContainer::initialize([
            'A' => [
                'containerClass' => 'red',
            ],
            'B' => [
                'containerClass' => 'green',
            ],
        ]);

        $result = FieldWithTheme::errorSummary(TestForm::validated(), theme: 'B')
            ->onlyProperties('name')
            ->render();

        $expected = <<<HTML
            <div class="green">
            <ul>
            <li>Value cannot be blank.</li>
            <li>This value must contain at least 4 characters.</li>
            </ul>
            </div>
            HTML;

        $this->assertSame($expected, $result);
    }

    public function testFieldset(): void
    {
        $result = Field::fieldset()
            ->legend('Choose your color')
            ->render();

        $expected = <<<'HTML'
        <div>
        <fieldset>
        <legend>Choose your color</legend>
        </fieldset>
        </div>
        HTML;

        $this->assertSame($expected, $result);
    }

    public function testFieldsetWithTheme(): void
    {
        ThemeContainer::initialize([
            'A' => [
                'containerClass' => 'red',
            ],
            'B' => [
                'containerClass' => 'green',
            ],
        ]);

        $result = FieldWithTheme::fieldset(theme: 'B')
            ->legend('Choose your color')
            ->render();

        $expected = <<<'HTML'
        <div class="green">
        <fieldset>
        <legend>Choose your color</legend>
        </fieldset>
        </div>
        HTML;

        $this->assertSame($expected, $result);
    }

    public function testFile(): void
    {
        $result = Field::file(new TestForm(), 'avatar')->render();
        $this->assertSame(
            <<<HTML
            <div>
            <label for="testform-avatar">Avatar</label>
            <input type="file" id="testform-avatar" name="TestForm[avatar]">
            </div>
            HTML,
            $result
        );
    }

    public function testFileWithTheme(): void
    {
        ThemeContainer::initialize([
            'A' => [
                'containerClass' => 'red',
            ],
            'B' => [
                'containerClass' => 'green',
            ],
        ]);

        $result = FieldWithTheme::file(new TestForm(), 'avatar', theme: 'B')->render();
        $this->assertSame(
            <<<HTML
            <div class="green">
            <label for="testform-avatar">Avatar</label>
            <input type="file" id="testform-avatar" name="TestForm[avatar]">
            </div>
            HTML,
            $result
        );
    }

    public function testHidden(): void
    {
        $result = Field::hidden(new TestForm(), 'key')->render();
        $this->assertSame(
            '<input type="hidden" id="testform-key" name="TestForm[key]" value="x100">',
            $result
        );
    }

    public function testHiddenWithTheme(): void
    {
        ThemeContainer::initialize([
            'A' => [
                'inputClass' => 'controlA',
            ],
            'B' => [
                'inputClass' => 'controlB',
            ],
        ]);

        $result = FieldWithTheme::hidden(new TestForm(), 'key', theme: 'B')->render();
        $this->assertSame(
            '<input type="hidden" id="testform-key" class="controlB" name="TestForm[key]" value="x100">',
            $result
        );
    }

    public function testImage(): void
    {
        $result = Field::image()
            ->src('btn.png')
            ->alt('Go')
            ->render();
        $this->assertSame(
            <<<HTML
            <div>
            <input type="image" src="btn.png" alt="Go">
            </div>
            HTML,
            $result
        );
    }

    public function testImageWithTheme(): void
    {
        ThemeContainer::initialize([
            'A' => [
                'containerClass' => 'red',
            ],
            'B' => [
                'containerClass' => 'green',
            ],
        ]);

        $result = FieldWithTheme::image(theme: 'B')
            ->src('btn.png')
            ->alt('Go')
            ->render();
        $this->assertSame(
            <<<HTML
            <div class="green">
            <input type="image" src="btn.png" alt="Go">
            </div>
            HTML,
            $result
        );
    }

    public function testImageWithUrl(): void
    {
        $html = Field::image('image.png')->render();

        $expected = <<<HTML
            <div>
            <input type="image" src="image.png">
            </div>
            HTML;

        $this->assertSame($expected, $html);
    }

    public function testNumber(): void
    {
        $result = Field::number(new TestForm(), 'age')->render();
        $this->assertSame(
            <<<HTML
            <div>
            <label for="testform-age">Your age</label>
            <input type="number" id="testform-age" name="TestForm[age]" value="42">
            <div>Full years.</div>
            </div>
            HTML,
            $result
        );
    }

    public function testNumberWithTheme(): void
    {
        ThemeContainer::initialize([
            'A' => [
                'containerClass' => 'red',
            ],
            'B' => [
                'containerClass' => 'green',
            ],
        ]);

        $result = FieldWithTheme::number(new TestForm(), 'age', theme: 'B')->render();
        $this->assertSame(
            <<<HTML
            <div class="green">
            <label for="testform-age">Your age</label>
            <input type="number" id="testform-age" name="TestForm[age]" value="42">
            <div>Full years.</div>
            </div>
            HTML,
            $result
        );
    }

    public function testPassword(): void
    {
        $result = Field::password(new TestForm(), 'oldPassword')->render();
        $this->assertSame(
            <<<HTML
            <div>
            <label for="testform-oldpassword">Old Password</label>
            <input type="password" id="testform-oldpassword" name="TestForm[oldPassword]" value>
            <div>Enter your old password.</div>
            </div>
            HTML,
            $result
        );
    }

    public function testPasswordWithTheme(): void
    {
        ThemeContainer::initialize([
            'A' => [
                'containerClass' => 'red',
            ],
            'B' => [
                'containerClass' => 'green',
            ],
        ]);

        $result = FieldWithTheme::password(new TestForm(), 'oldPassword', theme: 'B')->render();
        $this->assertSame(
            <<<HTML
            <div class="green">
            <label for="testform-oldpassword">Old Password</label>
            <input type="password" id="testform-oldpassword" name="TestForm[oldPassword]" value>
            <div>Enter your old password.</div>
            </div>
            HTML,
            $result
        );
    }

    public function testRadioList(): void
    {
        $result = Field::radioList(new TestForm(), 'color')
            ->items([
                'red' => 'Red',
                'blue' => 'Blue',
            ])
            ->render();

        $expected = <<<'HTML'
        <div>
        <label>Select color</label>
        <div>
        <label><input type="radio" name="TestForm[color]" value="red"> Red</label>
        <label><input type="radio" name="TestForm[color]" value="blue"> Blue</label>
        </div>
        <div>Color of box.</div>
        </div>
        HTML;

        $this->assertSame($expected, $result);
    }

    public function testRadioListWithTheme(): void
    {
        ThemeContainer::initialize([
            'A' => [
                'containerClass' => 'red',
            ],
            'B' => [
                'containerClass' => 'green',
            ],
        ]);

        $result = FieldWithTheme::radioList(new TestForm(), 'color', theme: 'B')
            ->items([
                'red' => 'Red',
                'blue' => 'Blue',
            ])
            ->render();

        $expected = <<<'HTML'
        <div class="green">
        <label>Select color</label>
        <div>
        <label><input type="radio" name="TestForm[color]" value="red"> Red</label>
        <label><input type="radio" name="TestForm[color]" value="blue"> Blue</label>
        </div>
        <div>Color of box.</div>
        </div>
        HTML;

        $this->assertSame($expected, $result);
    }

    public function testRange(): void
    {
        $result = Field::range(new TestForm(), 'volume')
            ->min(1)
            ->max(100)
            ->render();
        $this->assertSame(
            <<<HTML
            <div>
            <label for="testform-volume">Volume</label>
            <input type="range" id="testform-volume" name="TestForm[volume]" value="23" min="1" max="100">
            </div>
            HTML,
            $result
        );
    }

    public function testRangeWithTheme(): void
    {
        ThemeContainer::initialize([
            'A' => [
                'containerClass' => 'red',
            ],
            'B' => [
                'containerClass' => 'green',
            ],
        ]);

        $result = FieldWithTheme::range(new TestForm(), 'volume', theme: 'B')
            ->min(1)
            ->max(100)
            ->render();
        $this->assertSame(
            <<<HTML
            <div class="green">
            <label for="testform-volume">Volume</label>
            <input type="range" id="testform-volume" name="TestForm[volume]" value="23" min="1" max="100">
            </div>
            HTML,
            $result
        );
    }

    public function testResetButton(): void
    {
        $result = Field::resetButton('Reset form')->render();
        $this->assertSame(
            <<<HTML
            <div>
            <button type="reset">Reset form</button>
            </div>
            HTML,
            $result
        );
    }

    public function testResetButtonWithTheme(): void
    {
        ThemeContainer::initialize([
            'A' => [
                'containerClass' => 'red',
            ],
            'B' => [
                'containerClass' => 'green',
            ],
        ]);

        $result = FieldWithTheme::resetButton('Reset form', theme: 'B')->render();
        $this->assertSame(
            <<<HTML
            <div class="green">
            <button type="reset">Reset form</button>
            </div>
            HTML,
            $result
        );
    }

    public function testSelect(): void
    {
        $result = Field::select(new TestForm(), 'count')
            ->optionsData([
                1 => 'One',
                2 => 'Two',
            ])
            ->render();
        $this->assertSame(
            <<<HTML
            <div>
            <label for="testform-count">Select count</label>
            <select id="testform-count" name="TestForm[count]">
            <option value="1">One</option>
            <option value="2">Two</option>
            </select>
            </div>
            HTML,
            $result
        );
    }

    public function testSelectWithTheme(): void
    {
        ThemeContainer::initialize([
            'A' => [
                'containerClass' => 'red',
            ],
            'B' => [
                'containerClass' => 'green',
            ],
        ]);

        $result = FieldWithTheme::select(new TestForm(), 'count', theme: 'B')
            ->optionsData([
                1 => 'One',
                2 => 'Two',
            ])
            ->render();
        $this->assertSame(
            <<<HTML
            <div class="green">
            <label for="testform-count">Select count</label>
            <select id="testform-count" name="TestForm[count]">
            <option value="1">One</option>
            <option value="2">Two</option>
            </select>
            </div>
            HTML,
            $result
        );
    }

    public function testSubmitButton(): void
    {
        $result = Field::submitButton('Go!')->render();
        $this->assertSame(
            <<<HTML
            <div>
            <button type="submit">Go!</button>
            </div>
            HTML,
            $result
        );
    }

    public function testSubmitButtonWithTheme(): void
    {
        ThemeContainer::initialize([
            'A' => [
                'containerClass' => 'red',
            ],
            'B' => [
                'containerClass' => 'green',
            ],
        ]);

        $result = FieldWithTheme::submitButton('Go!', theme: 'B')->render();
        $this->assertSame(
            <<<HTML
            <div class="green">
            <button type="submit">Go!</button>
            </div>
            HTML,
            $result
        );
    }

    public function testTelephone(): void
    {
        $result = Field::telephone(new TestForm(), 'number')->render();
        $this->assertSame(
            <<<HTML
            <div>
            <label for="testform-number">Phone</label>
            <input type="tel" id="testform-number" name="TestForm[number]" value>
            <div>Enter your phone.</div>
            </div>
            HTML,
            $result
        );
    }

    public function testTelephoneWithTheme(): void
    {
        ThemeContainer::initialize([
            'A' => [
                'containerClass' => 'red',
            ],
            'B' => [
                'containerClass' => 'green',
            ],
        ]);

        $result = FieldWithTheme::telephone(new TestForm(), 'number', theme: 'B')->render();
        $this->assertSame(
            <<<HTML
            <div class="green">
            <label for="testform-number">Phone</label>
            <input type="tel" id="testform-number" name="TestForm[number]" value>
            <div>Enter your phone.</div>
            </div>
            HTML,
            $result
        );
    }

    public function testText(): void
    {
        $result = Field::text(new TestForm(), 'name')->render();
        $this->assertSame(
            <<<HTML
            <div>
            <label for="testform-name">Name</label>
            <input type="text" id="testform-name" name="TestForm[name]" value>
            <div>Input your full name.</div>
            </div>
            HTML,
            $result
        );
    }

    public function testTextWithTheme(): void
    {
        ThemeContainer::initialize([
            'A' => [
                'containerClass' => 'red',
            ],
            'B' => [
                'containerClass' => 'green',
            ],
        ]);

        $result = FieldWithTheme::text(new TestForm(), 'name', theme: 'B')->render();
        $this->assertSame(
            <<<HTML
            <div class="green">
            <label for="testform-name">Name</label>
            <input type="text" id="testform-name" name="TestForm[name]" value>
            <div>Input your full name.</div>
            </div>
            HTML,
            $result
        );
    }

    public function testTextarea(): void
    {
        $result = Field::textarea(new TestForm(), 'desc')->render();
        $this->assertSame(
            <<<HTML
            <div>
            <label for="testform-desc">Description</label>
            <textarea id="testform-desc" name="TestForm[desc]"></textarea>
            </div>
            HTML,
            $result
        );
    }

    public function testTextareaWithTheme(): void
    {
        ThemeContainer::initialize([
            'A' => [
                'containerClass' => 'red',
            ],
            'B' => [
                'containerClass' => 'green',
            ],
        ]);

        $result = FieldWithTheme::textarea(new TestForm(), 'desc', theme: 'B')->render();
        $this->assertSame(
            <<<HTML
            <div class="green">
            <label for="testform-desc">Description</label>
            <textarea id="testform-desc" name="TestForm[desc]"></textarea>
            </div>
            HTML,
            $result
        );
    }

    public function testTime(): void
    {
        $html = Field::time(new TestForm(), 'startTime')->render();

        $expected = <<<HTML
            <div>
            <label for="testform-starttime">Start Time</label>
            <input type="time" id="testform-starttime" name="TestForm[startTime]" value="14:00:23">
            </div>
            HTML;

        $this->assertSame($expected, $html);
    }

    public function testTimeWithTheme(): void
    {
        ThemeContainer::initialize([
            'A' => [
                'containerClass' => 'red',
            ],
            'B' => [
                'containerClass' => 'green',
            ],
        ]);

        $html = FieldWithTheme::time(new TestForm(), 'startTime', theme: 'B')->render();

        $expected = <<<HTML
            <div class="green">
            <label for="testform-starttime">Start Time</label>
            <input type="time" id="testform-starttime" name="TestForm[startTime]" value="14:00:23">
            </div>
            HTML;

        $this->assertSame($expected, $html);
    }

    public function testUrl(): void
    {
        $result = Field::url(new TestForm(), 'site')->render();
        $this->assertSame(
            <<<HTML
            <div>
            <label for="testform-site">Your site</label>
            <input type="url" id="testform-site" name="TestForm[site]" value>
            <div>Enter your site URL.</div>
            </div>
            HTML,
            $result
        );
    }

    public function testUrlWithTheme(): void
    {
        ThemeContainer::initialize([
            'A' => [
                'containerClass' => 'red',
            ],
            'B' => [
                'containerClass' => 'green',
            ],
        ]);

        $result = FieldWithTheme::url(new TestForm(), 'site', theme: 'B')->render();
        $this->assertSame(
            <<<HTML
            <div class="green">
            <label for="testform-site">Your site</label>
            <input type="url" id="testform-site" name="TestForm[site]" value>
            <div>Enter your site URL.</div>
            </div>
            HTML,
            $result
        );
    }

    public function testLabel(): void
    {
        $result = Field::label(new TestForm(), 'name')->render();
        $this->assertSame('<label for="testform-name">Name</label>', $result);
    }

    public function testLabelWithTheme(): void
    {
        ThemeContainer::initialize([
            'A' => [
                'labelClass' => 'red',
            ],
            'B' => [
                'labelClass' => 'green',
            ],
        ]);

        $result = FieldWithTheme::label(new TestForm(), 'name', theme: 'B')->render();
        $this->assertSame('<label class="green" for="testform-name">Name</label>', $result);
    }

    public function testHint(): void
    {
        $result = Field::hint(new TestForm(), 'name')->render();
        $this->assertSame('<div>Input your full name.</div>', $result);
    }

    public function testHintWithTheme(): void
    {
        ThemeContainer::initialize([
            'A' => [
                'hintClass' => 'red',
            ],
            'B' => [
                'hintClass' => 'green',
            ],
        ]);

        $result = FieldWithTheme::hint(new TestForm(), 'name', theme: 'B')->render();

        $this->assertSame('<div class="green">Input your full name.</div>', $result);
    }

    public function testError(): void
    {
        $result = Field::error(TestForm::validated(), 'name')->render();

        $expected = <<<HTML
            <div>
            Value cannot be blank.
            <br>
            This value must contain at least 4 characters.
            </div>
            HTML;

        $this->assertSame($expected, $result);
    }

    public function testErrorWithTheme(): void
    {
        ThemeContainer::initialize([
            'A' => [
                'errorClass' => 'red',
            ],
            'B' => [
                'errorClass' => 'green',
            ],
        ]);

        $result = FieldWithTheme::error(TestForm::validated(), 'name', theme: 'B')->render();

        $expected = <<<HTML
            <div class="green">
            Value cannot be blank.
            <br>
            This value must contain at least 4 characters.
            </div>
            HTML;

        $this->assertSame($expected, $result);
    }
}
