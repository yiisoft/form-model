<?php

declare(strict_types=1);

namespace Yiisoft\FormModel;

use Yiisoft\Form\Field\Button;
use Yiisoft\Form\Field\ButtonGroup;
use Yiisoft\Form\Field\Checkbox;
use Yiisoft\Form\Field\CheckboxList;
use Yiisoft\Form\Field\Date;
use Yiisoft\Form\Field\DateTime;
use Yiisoft\Form\Field\DateTimeLocal;
use Yiisoft\Form\Field\Email;
use Yiisoft\Form\Field\ErrorSummary;
use Yiisoft\Form\Field\Time;
use Yiisoft\Form\Field\Fieldset;
use Yiisoft\Form\Field\File;
use Yiisoft\Form\Field\Hidden;
use Yiisoft\Form\Field\Image;
use Yiisoft\Form\Field\Number;
use Yiisoft\Form\Field\Part\Error;
use Yiisoft\Form\Field\Part\Hint;
use Yiisoft\Form\Field\Part\Label;
use Yiisoft\Form\Field\Password;
use Yiisoft\Form\Field\RadioList;
use Yiisoft\Form\Field\Range;
use Yiisoft\Form\Field\ResetButton;
use Yiisoft\Form\Field\Select;
use Yiisoft\Form\Field\SubmitButton;
use Yiisoft\Form\Field\Telephone;
use Yiisoft\Form\Field\Text;
use Yiisoft\Form\Field\Textarea;
use Yiisoft\Form\Field\Url;

/**
 * @psalm-import-type Errors from ErrorSummary
 */
class FieldFactory
{
    final public function __construct(
        protected readonly ?string $defaultTheme = null,
    ) {
    }

    final public function button(?string $content = null, array $config = [], ?string $theme = null): Button
    {
        $field = Button::widget(config: $config, theme: $theme ?? $this->defaultTheme);

        if ($content !== null) {
            $field = $field->content($content);
        }

        return $field;
    }

    final public function buttonGroup(array $config = [], ?string $theme = null): ButtonGroup
    {
        return ButtonGroup::widget(config: $config, theme: $theme ?? $this->defaultTheme);
    }

    final public function checkbox(
        FormModelInterface $formModel,
        string $property,
        array $config = [],
        ?string $theme = null,
    ): Checkbox {
        return Checkbox::widget(config: $config, theme: $theme ?? $this->defaultTheme)
            ->inputData(new FormModelInputData($formModel, $property));
    }

    final public function checkboxList(
        FormModelInterface $formModel,
        string $property,
        array $config = [],
        ?string $theme = null,
    ): CheckboxList {
        return CheckboxList::widget(config: $config, theme: $theme ?? $this->defaultTheme)
            ->inputData(new FormModelInputData($formModel, $property));
    }

    final public function date(
        FormModelInterface $formModel,
        string $property,
        array $config = [],
        ?string $theme = null,
    ): Date {
        return Date::widget(config: $config, theme: $theme ?? $this->defaultTheme)
            ->inputData(new FormModelInputData($formModel, $property));
    }

    final public function dateTime(
        FormModelInterface $formModel,
        string $property,
        array $config = [],
        ?string $theme = null,
    ): DateTime {
        return DateTime::widget(config: $config, theme: $theme ?? $this->defaultTheme)
            ->inputData(new FormModelInputData($formModel, $property));
    }

    final public function dateTimeLocal(
        FormModelInterface $formModel,
        string $property,
        array $config = [],
        ?string $theme = null,
    ): DateTimeLocal {
        return DateTimeLocal::widget(config: $config, theme: $theme ?? $this->defaultTheme)
            ->inputData(new FormModelInputData($formModel, $property));
    }

    final public function email(
        FormModelInterface $formModel,
        string $property,
        array $config = [],
        ?string $theme = null,
    ): Email {
        return Email::widget(config: $config, theme: $theme ?? $this->defaultTheme)
            ->inputData(new FormModelInputData($formModel, $property));
    }

    final public function errorSummary(
        ?FormModelInterface $formModel = null,
        array $config = [],
        ?string $theme = null,
    ): ErrorSummary {
        $widget = ErrorSummary::widget(config: $config, theme: $theme ?? $this->defaultTheme);
        if ($formModel !== null) {
            $widget = $widget->errors($formModel->getValidationResult()?->getErrorMessagesIndexedByAttribute() ?? []);
        }
        return $widget;
    }

    final public function fieldset(array $config = [], ?string $theme = null): Fieldset
    {
        return Fieldset::widget(config: $config, theme: $theme ?? $this->defaultTheme);
    }

    final public function file(
        FormModelInterface $formModel,
        string $property,
        array $config = [],
        ?string $theme = null,
    ): File {
        return File::widget(config: $config, theme: $theme ?? $this->defaultTheme)
            ->inputData(new FormModelInputData($formModel, $property));
    }

    final public function hidden(
        FormModelInterface $formModel,
        string $property,
        array $config = [],
        ?string $theme = null,
    ): Hidden {
        return Hidden::widget(config: $config, theme: $theme ?? $this->defaultTheme)
            ->inputData(new FormModelInputData($formModel, $property));
    }

    final public function image(?string $url = null, array $config = [], ?string $theme = null): Image
    {
        $field = Image::widget(config: $config, theme: $theme ?? $this->defaultTheme);

        if ($url !== null) {
            $field = $field->src($url);
        }

        return $field;
    }

    final public function number(
        FormModelInterface $formModel,
        string $property,
        array $config = [],
        ?string $theme = null,
    ): Number {
        return Number::widget(config: $config, theme: $theme ?? $this->defaultTheme)
            ->inputData(new FormModelInputData($formModel, $property));
    }

    final public function password(
        FormModelInterface $formModel,
        string $property,
        array $config = [],
        ?string $theme = null,
    ): Password {
        return Password::widget(config: $config, theme: $theme ?? $this->defaultTheme)
            ->inputData(new FormModelInputData($formModel, $property));
    }

    final public function radioList(
        FormModelInterface $formModel,
        string $property,
        array $config = [],
        ?string $theme = null,
    ): RadioList {
        return RadioList::widget(config: $config, theme: $theme ?? $this->defaultTheme)
            ->inputData(new FormModelInputData($formModel, $property));
    }

    final public function range(
        FormModelInterface $formModel,
        string $property,
        array $config = [],
        ?string $theme = null,
    ): Range {
        return Range::widget(config: $config, theme: $theme ?? $this->defaultTheme)
            ->inputData(new FormModelInputData($formModel, $property));
    }

    final public function resetButton(
        ?string $content = null,
        array $config = [],
        ?string $theme = null,
    ): ResetButton {
        $field = ResetButton::widget(config: $config, theme: $theme ?? $this->defaultTheme);

        if ($content !== null) {
            $field = $field->content($content);
        }

        return $field;
    }

    final public function select(
        FormModelInterface $formModel,
        string $property,
        array $config = [],
        ?string $theme = null,
    ): Select {
        return Select::widget(config: $config, theme: $theme ?? $this->defaultTheme)
            ->inputData(new FormModelInputData($formModel, $property));
    }

    final public function submitButton(
        ?string $content = null,
        array $config = [],
        ?string $theme = null,
    ): SubmitButton {
        $field = SubmitButton::widget(config: $config, theme: $theme ?? $this->defaultTheme);

        if ($content !== null) {
            $field = $field->content($content);
        }

        return $field;
    }

    final public function telephone(
        FormModelInterface $formModel,
        string $property,
        array $config = [],
        ?string $theme = null,
    ): Telephone {
        return Telephone::widget(config: $config, theme: $theme ?? $this->defaultTheme)
            ->inputData(new FormModelInputData($formModel, $property));
    }

    final public function text(
        FormModelInterface $formModel,
        string $property,
        array $config = [],
        ?string $theme = null,
    ): Text {
        return Text::widget(config: $config, theme: $theme ?? $this->defaultTheme)
            ->inputData(new FormModelInputData($formModel, $property));
    }

    final public function textarea(
        FormModelInterface $formModel,
        string $property,
        array $config = [],
        ?string $theme = null,
    ): Textarea {
        return Textarea::widget(config: $config, theme: $theme ?? $this->defaultTheme)
            ->inputData(new FormModelInputData($formModel, $property));
    }

    final public function time(
        FormModelInterface $formModel,
        string $property,
        array $config = [],
        ?string $theme = null,
    ): Time {
        return Time::widget(config: $config, theme: $theme ?? $this->defaultTheme)
            ->inputData(new FormModelInputData($formModel, $property));
    }

    final public function url(
        FormModelInterface $formModel,
        string $property,
        array $config = [],
        ?string $theme = null,
    ): Url {
        return Url::widget(config: $config, theme: $theme ?? $this->defaultTheme)
            ->inputData(new FormModelInputData($formModel, $property));
    }

    final public function label(
        FormModelInterface $formModel,
        string $property,
        array $config = [],
        ?string $theme = null,
    ): Label {
        return Label::widget(config: $config, theme: $theme ?? $this->defaultTheme)
            ->inputData(new FormModelInputData($formModel, $property));
    }

    final public function hint(
        FormModelInterface $formModel,
        string $property,
        array $config = [],
        ?string $theme = null,
    ): Hint {
        return Hint::widget(config: $config, theme: $theme ?? $this->defaultTheme)
            ->inputData(new FormModelInputData($formModel, $property));
    }

    final public function error(
        FormModelInterface $formModel,
        string $property,
        array $config = [],
        ?string $theme = null,
    ): Error {
        return Error::widget(config: $config, theme: $theme ?? $this->defaultTheme)
            ->inputData(new FormModelInputData($formModel, $property));
    }
}
