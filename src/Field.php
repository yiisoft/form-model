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
 * Field is a shortcut static factory to ease creation of fields of various builtin types.
 */
class Field
{
    /**
     * @var string|null Default theme to use if it is not specified explicitly. Override in child classes.
     * @psalm-suppress MissingClassConstType Add constant type after bump PHP version to 8.3.
     */
    protected const DEFAULT_THEME = null;

    /**
     * Create a button field.
     *
     * @param string|null $content Button content.
     * @param array $config Widget config.
     * @param string|null $theme Theme to use. If not specified, default theme is used.
     * @return Button
     */
    final public static function button(?string $content = null, array $config = [], ?string $theme = null): Button
    {
        $field = Button::widget(config: $config, theme: $theme ?? static::DEFAULT_THEME);

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
    final public static function buttonGroup(array $config = [], ?string $theme = null): ButtonGroup
    {
        return ButtonGroup::widget(config: $config, theme: $theme ?? static::DEFAULT_THEME);
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
    final public static function checkbox(
        FormModelInterface $formModel,
        string $property,
        array $config = [],
        ?string $theme = null,
    ): Checkbox {
        return Checkbox::widget(config: $config, theme: $theme ?? static::DEFAULT_THEME)
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
    final public static function checkboxList(
        FormModelInterface $formModel,
        string $property,
        array $config = [],
        ?string $theme = null,
    ): CheckboxList {
        return CheckboxList::widget(config: $config, theme: $theme ?? static::DEFAULT_THEME)
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
    final public static function date(
        FormModelInterface $formModel,
        string $property,
        array $config = [],
        ?string $theme = null,
    ): Date {
        return Date::widget(config: $config, theme: $theme ?? static::DEFAULT_THEME)
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
    final public static function dateTimeLocal(
        FormModelInterface $formModel,
        string $property,
        array $config = [],
        ?string $theme = null,
    ): DateTimeLocal {
        return DateTimeLocal::widget(config: $config, theme: $theme ?? static::DEFAULT_THEME)
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
    final public static function email(
        FormModelInterface $formModel,
        string $property,
        array $config = [],
        ?string $theme = null,
    ): Email {
        return Email::widget(config: $config, theme: $theme ?? static::DEFAULT_THEME)
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
    final public static function errorSummary(
        ?FormModelInterface $formModel = null,
        array $config = [],
        ?string $theme = null,
    ): ErrorSummary {
        $widget = ErrorSummary::widget(config: $config, theme: $theme ?? static::DEFAULT_THEME);
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
    final public static function fieldset(array $config = [], ?string $theme = null): Fieldset
    {
        return Fieldset::widget(config: $config, theme: $theme ?? static::DEFAULT_THEME);
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
    final public static function file(
        FormModelInterface $formModel,
        string $property,
        array $config = [],
        ?string $theme = null,
    ): File {
        return File::widget(config: $config, theme: $theme ?? static::DEFAULT_THEME)
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
    final public static function hidden(
        FormModelInterface $formModel,
        string $property,
        array $config = [],
        ?string $theme = null,
    ): Hidden {
        return Hidden::widget(config: $config, theme: $theme ?? static::DEFAULT_THEME)
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
    final public static function image(?string $url = null, array $config = [], ?string $theme = null): Image
    {
        $field = Image::widget(config: $config, theme: $theme ?? static::DEFAULT_THEME);

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
    final public static function number(
        FormModelInterface $formModel,
        string $property,
        array $config = [],
        ?string $theme = null,
    ): Number {
        return Number::widget(config: $config, theme: $theme ?? static::DEFAULT_THEME)
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
    final public static function password(
        FormModelInterface $formModel,
        string $property,
        array $config = [],
        ?string $theme = null,
    ): Password {
        return Password::widget(config: $config, theme: $theme ?? static::DEFAULT_THEME)
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
    final public static function radioList(
        FormModelInterface $formModel,
        string $property,
        array $config = [],
        ?string $theme = null,
    ): RadioList {
        return RadioList::widget(config: $config, theme: $theme ?? static::DEFAULT_THEME)
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
    final public static function range(
        FormModelInterface $formModel,
        string $property,
        array $config = [],
        ?string $theme = null,
    ): Range {
        return Range::widget(config: $config, theme: $theme ?? static::DEFAULT_THEME)
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
    final public static function resetButton(
        ?string $content = null,
        array $config = [],
        ?string $theme = null,
    ): ResetButton {
        $field = ResetButton::widget(config: $config, theme: $theme ?? static::DEFAULT_THEME);

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
    final public static function select(
        FormModelInterface $formModel,
        string $property,
        array $config = [],
        ?string $theme = null,
    ): Select {
        return Select::widget(config: $config, theme: $theme ?? static::DEFAULT_THEME)
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
    final public static function submitButton(
        ?string $content = null,
        array $config = [],
        ?string $theme = null,
    ): SubmitButton {
        $field = SubmitButton::widget(config: $config, theme: $theme ?? static::DEFAULT_THEME);

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
    final public static function telephone(
        FormModelInterface $formModel,
        string $property,
        array $config = [],
        ?string $theme = null,
    ): Telephone {
        return Telephone::widget(config: $config, theme: $theme ?? static::DEFAULT_THEME)
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
    final public static function text(
        FormModelInterface $formModel,
        string $property,
        array $config = [],
        ?string $theme = null,
    ): Text {
        return Text::widget(config: $config, theme: $theme ?? static::DEFAULT_THEME)
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
    final public static function textarea(
        FormModelInterface $formModel,
        string $property,
        array $config = [],
        ?string $theme = null,
    ): Textarea {
        return Textarea::widget(config: $config, theme: $theme ?? static::DEFAULT_THEME)
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
    final public static function time(
        FormModelInterface $formModel,
        string $property,
        array $config = [],
        ?string $theme = null,
    ): Time {
        return Time::widget(config: $config, theme: $theme ?? static::DEFAULT_THEME)
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
    final public static function url(
        FormModelInterface $formModel,
        string $property,
        array $config = [],
        ?string $theme = null,
    ): Url {
        return Url::widget(config: $config, theme: $theme ?? static::DEFAULT_THEME)
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
    final public static function label(
        FormModelInterface $formModel,
        string $property,
        array $config = [],
        ?string $theme = null,
    ): Label {
        return Label::widget(config: $config, theme: $theme ?? static::DEFAULT_THEME)
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
    final public static function hint(
        FormModelInterface $formModel,
        string $property,
        array $config = [],
        ?string $theme = null,
    ): Hint {
        return Hint::widget(config: $config, theme: $theme ?? static::DEFAULT_THEME)
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
    final public static function error(
        FormModelInterface $formModel,
        string $property,
        array $config = [],
        ?string $theme = null,
    ): Error {
        return Error::widget(config: $config, theme: $theme ?? static::DEFAULT_THEME)
            ->inputData(new FormModelInputData($formModel, $property));
    }
}
