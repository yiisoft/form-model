<?php

declare(strict_types=1);

namespace Yiisoft\FormModel;

use InvalidArgumentException;
use Yiisoft\Form\Field\Base\InputData\InputDataInterface;
use Yiisoft\FormModel\Exception\PropertyNotSupportNestedValuesException;
use Yiisoft\FormModel\Exception\StaticObjectPropertyException;
use Yiisoft\FormModel\Exception\UndefinedObjectPropertyException;
use Yiisoft\FormModel\Exception\ValueNotFoundException;
use Yiisoft\Validator\Helper\RulesNormalizer;

use function sprintf;

/**
 * @psalm-import-type NormalizedRulesList from RulesNormalizer
 */
final class FormModelInputData implements InputDataInterface
{
    /**
     * @psalm-var NormalizedRulesList|null
     */
    private ?iterable $validationRules = null;
    private ParsedProperty $property;

    public function __construct(
        private readonly FormModelInterface $model,
        string $property,
    ) {
        $this->property = new ParsedProperty($property);
    }

    /**
     * @psalm-return NormalizedRulesList
     */
    public function getValidationRules(): iterable
    {
        if ($this->validationRules === null) {
            $rules = RulesNormalizer::normalize(null, $this->model);
            $this->validationRules = $rules[$this->property->name] ?? [];
        }
        return $this->validationRules;
    }

    /**
     * Generates an appropriate input name.
     *
     * This method generates a name that can be used as the input name to collect user input. The name is generated
     * according to the form and the property names. For example, if the form name is `Post`
     * then the input name generated for the `content` property would be `Post[content]`.
     *
     * See {@see getPropertyName()} for explanation of property expression.
     *
     * @throws InvalidArgumentException If the property name contains non-word characters or empty form name for
     * tabular inputs.
     * @return string The generated input name.
     */
    public function getName(): string
    {
        $formName = $this->model->getFormName();

        if ($formName === '' && $this->property->prefix === '') {
            return $this->property->raw;
        }

        if ($formName !== '') {
            return sprintf(
                '%s%s[%s]%s',
                $formName,
                $this->property->prefix,
                $this->property->name,
                $this->property->suffix
            );
        }

        throw new InvalidArgumentException('Form name cannot be empty for tabular inputs.');
    }

    /**
     * @throws UndefinedObjectPropertyException
     * @throws StaticObjectPropertyException
     * @throws PropertyNotSupportNestedValuesException
     * @throws ValueNotFoundException
     */
    public function getValue(): mixed
    {
        return $this->model->getPropertyValue($this->property->name . $this->property->suffix);
    }

    public function getLabel(): ?string
    {
        return $this->model->getPropertyLabel($this->getPropertyName() . $this->property->suffix);
    }

    public function getHint(): ?string
    {
        return $this->model->getPropertyHint($this->getPropertyName());
    }

    public function getPlaceholder(): ?string
    {
        $placeholder = $this->model->getPropertyPlaceholder($this->getPropertyName());
        return $placeholder === '' ? null : $placeholder;
    }

    /**
     * Generates an appropriate input ID.
     *
     * This method converts the result {@see getName()} into a valid input ID.
     *
     * For example, if {@see getInputName()} returns `Post[content]`, this method will return `post-content`.
     *
     * @throws InvalidArgumentException If the property name contains non-word characters.
     * @return string The generated input ID.
     */
    public function getId(): string
    {
        $name = $this->getName();
        $name = mb_strtolower($name, 'UTF-8');
        return str_replace(['[]', '][', '[', ']', ' ', '.'], ['', '-', '-', '', '-', '-'], $name);
    }

    public function isValidated(): bool
    {
        return $this->model->isValidated();
    }

    public function getValidationErrors(): array
    {
        /** @psalm-var list<string> */
        return $this->model->isValidated()
            ? $this->model->getValidationResult()->getPropertyErrorMessagesByPath($this->property->path)
            : [];
    }

    private function getPropertyName(): string
    {
        $property = $this->property->name;

        if (!$this->model->hasProperty($property)) {
            throw new InvalidArgumentException('Property "' . $property . '" does not exist.');
        }

        return $property;
    }

    /**
     * This method parses a property expression and returns an associative array containing
     * real property name, prefix and suffix.
     * For example: `['name' => 'content', 'prefix' => '', 'suffix' => '[0]']`
     *
     * A property expression is a property name prefixed and/or suffixed with array indexes. It is mainly used in
     * tabular data input and/or input of array type. Below are some examples:
     *
     * - `[0]content` is used in tabular data input to represent the "content" property for the first model in tabular
     *    input;
     * - `dates[0]` represents the first array element of the "dates" property;
     * - `[0]dates[0]` represents the first array element of the "dates" property for the first model in tabular
     *    input.
     *
     * @param string $property The property name or expression
     *
     * @throws InvalidArgumentException If the property name contains non-word characters.
     * @return string[] The property name, prefix and suffix.
     */
    private function parseProperty(string $property): array
    {
        if (!preg_match('/(^|.*\])([\w\.\+\-_]+)(\[.*|$)/u', $property, $matches)) {
            throw new InvalidArgumentException('Property name must contain word characters only.');
        }
        return [
            'name' => $matches[2],
            'prefix' => $matches[1],
            'suffix' => $matches[3],
        ];
    }
}
