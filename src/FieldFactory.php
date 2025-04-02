<?php

declare(strict_types=1);

namespace Yiisoft\FormModel;

use Yiisoft\Form\Field\Button;
use Yiisoft\Form\Field\ButtonGroup;
use Yiisoft\Form\Field\Checkbox;
use Yiisoft\Form\Field\CheckboxList;
use Yiisoft\Form\Field\Date;
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
 * `FieldFactory` is a factory to ease creation of fields of various builtin types.
 *
 * @psalm-suppress ClassMustBeFinal We allow to extend this class.
 * @psalm-import-type Errors from ErrorSummary
 */
class FieldFactory
{
    /**
     * @param string|null $defaultTheme Default theme to use when it is not specified explicitly.
     */
    final public function __construct(
        protected readonly ?string $defaultTheme = null,
    ) {
    }

    /**
     * Create a button field.
     *
     * @param string|null $content Button content.
     * @param array $config Widget config.
     * @param string|null $theme Theme to use. If not specified, default theme is used.
     * @return Button
     */
    final public function button(?string $content = null, array $config = [], ?string $theme = null): Button
    {
        $field = Button::widget(config: $config, theme: $theme ?? $this->defaultTheme);

        if ($content !== null) {
            $field = $field->content($content);
        }

        return $field;
    }

    /**
     * Create a button group field.
     *
     * @param array $config Widget config.
     * @param string|null $theme Theme to use. If not specified, default theme is used.
     * @return ButtonGroup
     */
    final public function buttonGroup(array $config = [], ?string $theme = null): ButtonGroup
    {
        return ButtonGroup::widget(config: $config, theme: $theme ?? $this->defaultTheme);
    }

    /**
     * Create a checkbox field.
     *
     * @param FormModelInterface $formModel Model to take value from.
     * @param string $property Model property name to take value from.
     * @param array $config Widget config.
     * @param string|null $theme Theme to use. If not specified, default theme is used.
     * @return Checkbox
     */
    final public function checkbox(
        FormModelInterface $formModel,
        string $property,
        array $config = [],
        ?string $theme = null,
    ): Checkbox {
        return Checkbox::widget(config: $config, theme: $theme ?? $this->defaultTheme)
            ->inputData(new FormModelInputData($formModel, $property));
    }

    /**
     * Create checkboxes list field.
     *
     * @param FormModelInterface $formModel Model to take value from.
     * @param string $property Model property name to take value from.
     * @param array $config Widget config.
     * @param string|null $theme Theme to use. If not specified, default theme is used.
     * @return CheckboxList
     */
    final public function checkboxList(
        FormModelInterface $formModel,
        string $property,
        array $config = [],
        ?string $theme = null,
    ): CheckboxList {
        return CheckboxList::widget(config: $config, theme: $theme ?? $this->defaultTheme)
            ->inputData(new FormModelInputData($formModel, $property));
    }

    /**
     * Create a date field.
     *
     * @param FormModelInterface $formModel Model to take value from.
     * @param string $property Model property name to take value from.
     * @param array $config Widget config.
     * @param string|null $theme Theme to use. If not specified, default theme is used.
     * @return Date
     */
    final public function date(
        FormModelInterface $formModel,
        string $property,
        array $config = [],
        ?string $theme = null,
    ): Date {
        return Date::widget(config: $config, theme: $theme ?? $this->defaultTheme)
            ->inputData(new FormModelInputData($formModel, $property));
    }

    /**
     * Create a local date and time field.
     *
     * @param FormModelInterface $formModel Model to take value from.
     * @param string $property Model property name to take value from.
     * @param array $config Widget config.
     * @param string|null $theme Theme to use. If not specified, default theme is used.
     * @return DateTimeLocal
     */
    final public function dateTimeLocal(
        FormModelInterface $formModel,
        string $property,
        array $config = [],
        ?string $theme = null,
    ): DateTimeLocal {
        return DateTimeLocal::widget(config: $config, theme: $theme ?? $this->defaultTheme)
            ->inputData(new FormModelInputData($formModel, $property));
    }

    /**
     * Create an email field.
     *
     * @param FormModelInterface $formModel Model to take value from.
     * @param string $property Model property name to take value from.
     * @param array $config Widget config.
     * @param string|null $theme Theme to use. If not specified, default theme is used.
     * @return Email
     */
    final public function email(
        FormModelInterface $formModel,
        string $property,
        array $config = [],
        ?string $theme = null,
    ): Email {
        return Email::widget(config: $config, theme: $theme ?? $this->defaultTheme)
            ->inputData(new FormModelInputData($formModel, $property));
    }

    /**
     * Create errors summary field.
     *
     * @param FormModelInterface|null $formModel Model to take errors from.
     * @param array $config Widget config.
     * @param string|null $theme Theme to use. If not specified, default theme is used.
     * @return ErrorSummary
     */
    final public function errorSummary(
        ?FormModelInterface $formModel = null,
        array $config = [],
        ?string $theme = null,
    ): ErrorSummary {
        $widget = ErrorSummary::widget(config: $config, theme: $theme ?? $this->defaultTheme);
        if ($formModel !== null) {
            $widget = $widget->errors(
                $formModel->isValidated()
                    ? $formModel->getValidationResult()->getErrorMessagesIndexedByProperty()
                    : []
            );
        }
        return $widget;
    }

    /**
     * Create a fieldset.
     *
     * @param array $config Widget config.
     * @param string|null $theme Theme to use. If not specified, default theme is used.
     * @return Fieldset
     */
    final public function fieldset(array $config = [], ?string $theme = null): Fieldset
    {
        return Fieldset::widget(config: $config, theme: $theme ?? $this->defaultTheme);
    }

    /**
     * Create a file upload field.
     *
     * @param FormModelInterface $formModel Model to take value from.
     * @param string $property Model property name to take value from.
     * @param array $config Widget config.
     * @param string|null $theme Theme to use. If not specified, default theme is used.
     * @return File
     */
    final public function file(
        FormModelInterface $formModel,
        string $property,
        array $config = [],
        ?string $theme = null,
    ): File {
        return File::widget(config: $config, theme: $theme ?? $this->defaultTheme)
            ->inputData(new FormModelInputData($formModel, $property));
    }

    /**
     * Create a hidden field.
     *
     * @param FormModelInterface $formModel Model to take value from.
     * @param string $property Model property name to take value from.
     * @param array $config Widget config.
     * @param string|null $theme Theme to use. If not specified, default theme is used.
     * @return Hidden
     */
    final public function hidden(
        FormModelInterface $formModel,
        string $property,
        array $config = [],
        ?string $theme = null,
    ): Hidden {
        return Hidden::widget(config: $config, theme: $theme ?? $this->defaultTheme)
            ->inputData(new FormModelInputData($formModel, $property));
    }

    /**
     * Create an image.
     *
     * @param string|null $url "src" of the image.
     * @param array $config Widget config.
     * @param string|null $theme Theme to use. If not specified, default theme is used.
     * @return Image
     */
    final public function image(?string $url = null, array $config = [], ?string $theme = null): Image
    {
        $field = Image::widget(config: $config, theme: $theme ?? $this->defaultTheme);

        if ($url !== null) {
            $field = $field->src($url);
        }

        return $field;
    }

    /**
     * Create a number field.
     *
     * @param FormModelInterface $formModel Model to take value from.
     * @param string $property Model property name to take value from.
     * @param array $config Widget config.
     * @param string|null $theme Theme to use. If not specified, default theme is used.
     * @return Number
     */
    final public function number(
        FormModelInterface $formModel,
        string $property,
        array $config = [],
        ?string $theme = null,
    ): Number {
        return Number::widget(config: $config, theme: $theme ?? $this->defaultTheme)
            ->inputData(new FormModelInputData($formModel, $property));
    }

    /**
     * Create a password field.
     *
     * @param FormModelInterface $formModel Model to take value from.
     * @param string $property Model property name to take value from.
     * @param array $config Widget config.
     * @param string|null $theme Theme to use. If not specified, default theme is used.
     * @return Password
     */
    final public function password(
        FormModelInterface $formModel,
        string $property,
        array $config = [],
        ?string $theme = null,
    ): Password {
        return Password::widget(config: $config, theme: $theme ?? $this->defaultTheme)
            ->inputData(new FormModelInputData($formModel, $property));
    }

    /**
     * Create a radio list field.
     *
     * @param FormModelInterface $formModel Model to take value from.
     * @param string $property Model property name to take value from.
     * @param array $config Widget config.
     * @param string|null $theme Theme to use. If not specified, default theme is used.
     * @return RadioList
     */
    final public function radioList(
        FormModelInterface $formModel,
        string $property,
        array $config = [],
        ?string $theme = null,
    ): RadioList {
        return RadioList::widget(config: $config, theme: $theme ?? $this->defaultTheme)
            ->inputData(new FormModelInputData($formModel, $property));
    }

    /**
     * Create a range field.
     *
     * @param FormModelInterface $formModel Model to take value from.
     * @param string $property Model property name to take value from.
     * @param array $config Widget config.
     * @param string|null $theme Theme to use. If not specified, default theme is used.
     * @return Range
     */
    final public function range(
        FormModelInterface $formModel,
        string $property,
        array $config = [],
        ?string $theme = null,
    ): Range {
        return Range::widget(config: $config, theme: $theme ?? $this->defaultTheme)
            ->inputData(new FormModelInputData($formModel, $property));
    }

    /**
     * Create a reset button.
     *
     * @param string|null $content Button content.
     * @param array $config Widget config.
     * @param string|null $theme Theme to use. If not specified, default theme is used.
     * @return ResetButton
     */
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

    /**
     * Create a select field.
     *
     * @param FormModelInterface $formModel Model to take value from.
     * @param string $property Model property name to take value from.
     * @param array $config Widget config.
     * @param string|null $theme Theme to use. If not specified, default theme is used.
     * @return Select
     */
    final public function select(
        FormModelInterface $formModel,
        string $property,
        array $config = [],
        ?string $theme = null,
    ): Select {
        return Select::widget(config: $config, theme: $theme ?? $this->defaultTheme)
            ->inputData(new FormModelInputData($formModel, $property));
    }

    /**
     * Create a submit button.
     *
     * @param string|null $content Button content.
     * @param array $config Widget config.
     * @param string|null $theme Theme to use. If not specified, default theme is used.
     * @return SubmitButton
     */
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

    /**
     * Create a phone number field.
     *
     * @param FormModelInterface $formModel Model to take value from.
     * @param string $property Model property name to take value from.
     * @param array $config Widget config.
     * @param string|null $theme Theme to use. If not specified, default theme is used.
     * @return Telephone
     */
    final public function telephone(
        FormModelInterface $formModel,
        string $property,
        array $config = [],
        ?string $theme = null,
    ): Telephone {
        return Telephone::widget(config: $config, theme: $theme ?? $this->defaultTheme)
            ->inputData(new FormModelInputData($formModel, $property));
    }

    /**
     * Create a text field.
     *
     * @param FormModelInterface $formModel Model to take value from.
     * @param string $property Model property name to take value from.
     * @param array $config Widget config.
     * @param string|null $theme Theme to use. If not specified, default theme is used.
     * @return Text
     */
    final public function text(
        FormModelInterface $formModel,
        string $property,
        array $config = [],
        ?string $theme = null,
    ): Text {
        return Text::widget(config: $config, theme: $theme ?? $this->defaultTheme)
            ->inputData(new FormModelInputData($formModel, $property));
    }

    /**
     * Create a text area field.
     *
     * @param FormModelInterface $formModel Model to take value from.
     * @param string $property Model property name to take value from.
     * @param array $config Widget config.
     * @param string|null $theme Theme to use. If not specified, default theme is used.
     * @return Textarea
     */
    final public function textarea(
        FormModelInterface $formModel,
        string $property,
        array $config = [],
        ?string $theme = null,
    ): Textarea {
        return Textarea::widget(config: $config, theme: $theme ?? $this->defaultTheme)
            ->inputData(new FormModelInputData($formModel, $property));
    }

    /**
     * Create a time field.
     *
     * @param FormModelInterface $formModel Model to take value from.
     * @param string $property Model property name to take value from.
     * @param array $config Widget config.
     * @param string|null $theme Theme to use. If not specified, default theme is used.
     * @return Time
     */
    final public function time(
        FormModelInterface $formModel,
        string $property,
        array $config = [],
        ?string $theme = null,
    ): Time {
        return Time::widget(config: $config, theme: $theme ?? $this->defaultTheme)
            ->inputData(new FormModelInputData($formModel, $property));
    }

    /**
     * Create a URL input field.
     *
     * @param FormModelInterface $formModel Model to take value from.
     * @param string $property Model property name to take value from.
     * @param array $config Widget config.
     * @param string|null $theme Theme to use. If not specified, default theme is used.
     * @return Url
     */
    final public function url(
        FormModelInterface $formModel,
        string $property,
        array $config = [],
        ?string $theme = null,
    ): Url {
        return Url::widget(config: $config, theme: $theme ?? $this->defaultTheme)
            ->inputData(new FormModelInputData($formModel, $property));
    }

    /**
     * Create a field label.
     *
     * @param FormModelInterface $formModel Model to create label for.
     * @param string $property Model property name to create label for.
     * @param array $config Widget config.
     * @param string|null $theme Theme to use. If not specified, default theme is used.
     * @return Label
     */
    final public function label(
        FormModelInterface $formModel,
        string $property,
        array $config = [],
        ?string $theme = null,
    ): Label {
        return Label::widget(config: $config, theme: $theme ?? $this->defaultTheme)
            ->inputData(new FormModelInputData($formModel, $property));
    }

    /**
     * Create a field hint.
     *
     * @param FormModelInterface $formModel Model to create hint for.
     * @param string $property Model property name to create hint for.
     * @param array $config Widget config.
     * @param string|null $theme Theme to use. If not specified, default theme is used.
     * @return Hint
     */
    final public function hint(
        FormModelInterface $formModel,
        string $property,
        array $config = [],
        ?string $theme = null,
    ): Hint {
        return Hint::widget(config: $config, theme: $theme ?? $this->defaultTheme)
            ->inputData(new FormModelInputData($formModel, $property));
    }

    /**
     * Create an error for a field.
     *
     * @param FormModelInterface $formModel Model to create error for.
     * @param string $property Model property name to create error for.
     * @param array $config Widget config.
     * @param string|null $theme Theme to use. If not specified, default theme is used.
     * @return Error
     */
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
