<?php

declare(strict_types=1);

namespace Yiisoft\FormModel\Tests;

use PHPUnit\Framework\TestCase;
use Yiisoft\Form\Theme\ThemeContainer;
use Yiisoft\FormModel\FieldFactory;
use Yiisoft\FormModel\Tests\Support\Form\TestForm;
use Yiisoft\Html\Html;

final class FieldFactoryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        ThemeContainer::initialize();
    }

    public function testButton(): void
    {
        $result = (new FieldFactory())->button('Show info')->render();
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

        $result = (new FieldFactory('A'))->button('Show info', theme: 'B')->render();
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
        $result = (new FieldFactory())->buttonGroup()
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

        $result = (new FieldFactory('A'))->buttonGroup(theme: 'B')
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
        $result = (new FieldFactory())->checkbox(new TestForm(), 'blue')->render();
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

        $result = (new FieldFactory('A'))->checkbox(new TestForm(), 'blue', theme: 'B')->render();
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
        $result = (new FieldFactory())->checkboxList(new TestForm(), 'color2')
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

        $result = (new FieldFactory('A'))->checkboxList(new TestForm(), 'color2', theme: 'B')
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
        $result = (new FieldFactory())->date(new TestForm(), 'birthday')->render();
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

        $result = (new FieldFactory('A'))->date(new TestForm(), 'birthday', theme: 'B')->render();
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
        $result = (new FieldFactory())->dateTimeLocal(new TestForm(), 'partyDate')->render();
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

        $result = (new FieldFactory('A'))->dateTimeLocal(new TestForm(), 'partyDate', theme: 'B')->render();
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
        $result = (new FieldFactory())->email(new TestForm(), 'mainEmail')->render();
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

        $result = (new FieldFactory('A'))->email(new TestForm(), 'mainEmail', theme: 'B')->render();
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
        $result = (new FieldFactory())->errorSummary(TestForm::validated())
            ->onlyProperties('name')
            ->render();

        $expected = <<<HTML
            <div>
            <ul>
            <li>Name cannot be blank.</li>
            <li>Name must contain at least 4 characters.</li>
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

        $result = (new FieldFactory('A'))->errorSummary(TestForm::validated(), theme: 'B')
            ->onlyProperties('name')
            ->render();

        $expected = <<<HTML
            <div class="green">
            <ul>
            <li>Name cannot be blank.</li>
            <li>Name must contain at least 4 characters.</li>
            </ul>
            </div>
            HTML;

        $this->assertSame($expected, $result);
    }

    public function testFieldset(): void
    {
        $result = (new FieldFactory())->fieldset()
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

        $result = (new FieldFactory('A'))->fieldset(theme: 'B')
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
        $result = (new FieldFactory())->file(new TestForm(), 'avatar')->render();
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

        $result = (new FieldFactory('A'))->file(new TestForm(), 'avatar', theme: 'B')->render();
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
        $result = (new FieldFactory())->hidden(new TestForm(), 'key')->render();
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

        $result = (new FieldFactory('A'))->hidden(new TestForm(), 'key', theme: 'B')->render();
        $this->assertSame(
            '<input type="hidden" id="testform-key" class="controlB" name="TestForm[key]" value="x100">',
            $result
        );
    }

    public function testImage(): void
    {
        $result = (new FieldFactory())->image()
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

        $result = (new FieldFactory('A'))->image(theme: 'B')
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
        $html = (new FieldFactory())->image('image.png')->render();

        $expected = <<<HTML
            <div>
            <input type="image" src="image.png">
            </div>
            HTML;

        $this->assertSame($expected, $html);
    }

    public function testNumber(): void
    {
        $result = (new FieldFactory())->number(new TestForm(), 'age')->render();
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

        $result = (new FieldFactory('A'))->number(new TestForm(), 'age', theme: 'B')->render();
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
        $result = (new FieldFactory())->password(new TestForm(), 'oldPassword')->render();
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

        $result = (new FieldFactory('A'))->password(new TestForm(), 'oldPassword', theme: 'B')->render();
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
        $result = (new FieldFactory())->radioList(new TestForm(), 'color')
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

        $result = (new FieldFactory('A'))->radioList(new TestForm(), 'color', theme: 'B')
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
        $result = (new FieldFactory())->range(new TestForm(), 'volume')
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

        $result = (new FieldFactory('A'))->range(new TestForm(), 'volume', theme: 'B')
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
        $result = (new FieldFactory())->resetButton('Reset form')->render();
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

        $result = (new FieldFactory('A'))->resetButton('Reset form', theme: 'B')->render();
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
        $result = (new FieldFactory())->select(new TestForm(), 'count')
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

        $result = (new FieldFactory('A'))->select(new TestForm(), 'count', theme: 'B')
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
        $result = (new FieldFactory())->submitButton('Go!')->render();
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

        $result = (new FieldFactory('A'))->submitButton('Go!', theme: 'B')->render();
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
        $result = (new FieldFactory())->telephone(new TestForm(), 'number')->render();
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

        $result = (new FieldFactory('A'))->telephone(new TestForm(), 'number', theme: 'B')->render();
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
        $result = (new FieldFactory())->text(new TestForm(), 'name')->render();
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

        $result = (new FieldFactory('A'))->text(new TestForm(), 'name', theme: 'B')->render();
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
        $result = (new FieldFactory())->textarea(new TestForm(), 'desc')->render();
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

        $result = (new FieldFactory('A'))->textarea(new TestForm(), 'desc', theme: 'B')->render();
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
        $html = (new FieldFactory())->time(new TestForm(), 'startTime')->render();

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

        $html = (new FieldFactory('A'))->time(new TestForm(), 'startTime', theme: 'B')->render();

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
        $result = (new FieldFactory())->url(new TestForm(), 'site')->render();
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

        $result = (new FieldFactory('A'))->url(new TestForm(), 'site', theme: 'B')->render();
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
        $result = (new FieldFactory())->label(new TestForm(), 'name')->render();
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

        $result = (new FieldFactory('A'))->label(new TestForm(), 'name', theme: 'B')->render();
        $this->assertSame('<label class="green" for="testform-name">Name</label>', $result);
    }

    public function testHint(): void
    {
        $result = (new FieldFactory())->hint(new TestForm(), 'name')->render();
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

        $result = (new FieldFactory('A'))->hint(new TestForm(), 'name', theme: 'B')->render();

        $this->assertSame('<div class="green">Input your full name.</div>', $result);
    }

    public function testError(): void
    {
        $result = (new FieldFactory())->error(TestForm::validated(), 'name')->render();

        $expected = <<<HTML
            <div>
            Name cannot be blank.
            <br>
            Name must contain at least 4 characters.
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

        $result = (new FieldFactory('A'))->error(TestForm::validated(), 'name', theme: 'B')->render();

        $expected = <<<HTML
            <div class="green">
            Name cannot be blank.
            <br>
            Name must contain at least 4 characters.
            </div>
            HTML;

        $this->assertSame($expected, $result);
    }
}
