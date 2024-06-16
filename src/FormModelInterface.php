<?php

declare(strict_types=1);

namespace Yiisoft\FormModel;

use Yiisoft\FormModel\Exception\PropertyNotSupportNestedValuesException;
use Yiisoft\FormModel\Exception\StaticObjectPropertyException;
use Yiisoft\FormModel\Exception\UndefinedObjectPropertyException;
use Yiisoft\FormModel\Exception\ValueNotFoundException;
use Yiisoft\Hydrator\Validator\ValidatedInputInterface;

/**
 * Form model represents an HTML form: its data, validation and presentation.
 */
interface FormModelInterface extends ValidatedInputInterface
{
    /**
     * Returns the text hint for the specified property.
     *
     * @param string $property The property name.
     *
     * @return string The property hint.
     */
    public function getPropertyHint(string $property): string;

    /**
     * Returns the property hints.
     *
     * Property hints are mainly used for display purpose. For example, given a property `isPublic`, we can declare
     * a hint `Whether the post should be visible for not logged-in users`, which provides user-friendly description of
     * the property meaning and can be displayed to end users.
     *
     * Unlike label hint will not be generated if its explicit declaration is omitted.
     *
     * Note, in order to inherit hints defined in the parent class, a child class needs to merge the parent hints with
     * child hints using functions such as `array_merge()`.
     *
     * @return array Property hints (name => hint).
     *
     * @psalm-return array<string,string>
     */
    public function getPropertyHints(): array;

    /**
     * Returns the text label for the specified property.
     *
     * @param string $property The property name.
     *
     * @return string The property label.
     */
    public function getPropertyLabel(string $property): string;

    /**
     * Returns the property labels.
     *
     * Property labels are mainly used for display purpose. For example, given a property `firstName`, we can
     * declare a label `First Name` which is more user-friendly and can be displayed to end users.
     *
     * By default, a property label is generated automatically. This method allows you to
     * explicitly specify property labels.
     *
     * Note, in order to inherit labels defined in the parent class, a child class needs to merge the parent labels
     * with child labels using functions such as `array_merge()`.
     *
     * @return array Property labels (name => label).
     *
     * {@see getPropertyLabel()}
     *
     * @psalm-return array<string,string>
     */
    public function getPropertyLabels(): array;

    /**
     * Returns the text placeholder for the specified property.
     *
     * @param string $property The property name.
     *
     * @return string The property placeholder.
     */
    public function getPropertyPlaceholder(string $property): string;

    /**
     * Get a value for a property specified.
     *
     * @param string $property Name of the property.
     * @throws UndefinedObjectPropertyException
     * @throws StaticObjectPropertyException
     * @throws PropertyNotSupportNestedValuesException
     * @throws ValueNotFoundException
     * @return mixed Value.
     */
    public function getPropertyValue(string $property): mixed;

    /**
     * Returns the property placeholders.
     *
     * @return array Property placeholder (name => placeholder).
     *
     * @psalm-return array<string,string>
     */
    public function getPropertyPlaceholders(): array;

    /**
     * Returns the form name that this model class should use.
     *
     * The form name is mainly used by {@see TODO: FIX NAMESPACE HtmlForm} to determine how to name the input
     * fields for the properties in a model.
     * If the form name is "A" and a property name is "b", then the corresponding input name would be "A[b]".
     * If the form name is an empty string, then the input name would be "b".
     *
     * The purpose of the above naming schema is that for forms which contain multiple different models, the properties
     * of each model are grouped in sub-arrays of the POST-data, and it is easier to differentiate between them.
     *
     * By default, this method returns the model class name (without the namespace part) as the form name. You may
     * override it when the model is used in different forms.
     *
     * @return string The form name of this model class.
     */
    public function getFormName(): string;

    /**
     * If there is such property in the set.
     *
     * @param string $property Property name.
     * @return bool Whether there's such property.
     */
    public function hasProperty(string $property): bool;

    /**
     * @return bool Whether form data is valid.
     */
    public function isValid(): bool;

    /**
     * @return bool Whether form was validated.
     */
    public function isValidated(): bool;
}
