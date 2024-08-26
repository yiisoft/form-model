<?php

declare(strict_types=1);

namespace Yiisoft\FormModel;

use LogicException;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;
use Yiisoft\FormModel\Attribute\Hint;
use Yiisoft\FormModel\Attribute\Placeholder;
use Yiisoft\FormModel\Exception\PropertyNotSupportNestedValuesException;
use Yiisoft\FormModel\Exception\StaticObjectPropertyException;
use Yiisoft\FormModel\Exception\UndefinedArrayElementException;
use Yiisoft\FormModel\Exception\UndefinedObjectPropertyException;
use Yiisoft\FormModel\Exception\ValueNotFoundException;
use Yiisoft\Hydrator\Attribute\SkipHydration;
use Yiisoft\Strings\Inflector;
use Yiisoft\Strings\StringHelper;
use Yiisoft\Validator\Label;
use Yiisoft\Validator\Result;

use function array_key_exists;
use function array_slice;
use function is_array;
use function is_object;
use function str_contains;
use function strrchr;
use function substr;

/**
 * A base class for form models that represent HTML forms: their data, validation and presentation.
 */
abstract class FormModel implements FormModelInterface
{
    /**
     * @psalm-suppress MissingClassConstType Remove after fix https://github.com/vimeo/psalm/issues/11026
     */
    private const META_LABEL = 1;

    /**
     * @psalm-suppress MissingClassConstType Remove after fix https://github.com/vimeo/psalm/issues/11026
     */
    private const META_HINT = 2;

    /**
     * @psalm-suppress MissingClassConstType Remove after fix https://github.com/vimeo/psalm/issues/11026
     */
    private const META_PLACEHOLDER = 3;

    /**
     * @var Result|null Validation result.
     */
    #[SkipHydration]
    private ?Result $validationResult = null;

    private static ?Inflector $inflector = null;

    public function getPropertyHint(string $property): string
    {
        return $this->readPropertyMetaValue(self::META_HINT, $property) ?? '';
    }

    public function getPropertyHints(): array
    {
        return [];
    }

    public function getPropertyLabel(string $property): string
    {
        return $this->readPropertyMetaValue(self::META_LABEL, $property) ?? $this->generatePropertyLabel($property);
    }

    public function getPropertyLabels(): array
    {
        return [];
    }

    public function getPropertyPlaceholder(string $property): string
    {
        return $this->readPropertyMetaValue(self::META_PLACEHOLDER, $property) ?? '';
    }

    public function getPropertyValue(string $property): mixed
    {
        try {
            return $this->readPropertyValue($property);
        } catch (PropertyNotSupportNestedValuesException $exception) {
            return $exception->getValue() === null
                ? null
                : throw $exception;
        } catch (UndefinedArrayElementException) {
            return null;
        }
    }

    public function getPropertyPlaceholders(): array
    {
        return [];
    }

    public function getFormName(): string
    {
        if (str_contains(static::class, '@anonymous')) {
            return '';
        }

        $className = strrchr(static::class, '\\');
        if ($className === false) {
            return static::class;
        }

        return substr($className, 1);
    }

    public function hasProperty(string $property): bool
    {
        try {
            $this->readPropertyValue($property);
        } catch (ValueNotFoundException) {
            return false;
        }
        return true;
    }

    public function isValid(): bool
    {
        return $this->isValidated() && $this->getValidationResult()->isValid();
    }

    public function isValidated(): bool
    {
        return $this->validationResult !== null;
    }

    public function addError(string $message, array $valuePath = []): static
    {
        $this->getValidationResult()->addErrorWithoutPostProcessing($message, valuePath: $valuePath);
        return $this;
    }

    public function processValidationResult(Result $result): void
    {
        $this->validationResult = $result;
    }

    public function getValidationResult(): Result
    {
        if (empty($this->validationResult)) {
            throw new LogicException('Validation result is not set.');
        }

        return $this->validationResult;
    }

    /**
     * Returns model property value given a path.
     *
     * @param string $path Property path.
     * @throws UndefinedArrayElementException
     * @throws UndefinedObjectPropertyException
     * @throws StaticObjectPropertyException
     * @throws PropertyNotSupportNestedValuesException
     * @throws ValueNotFoundException
     * @return mixed Property value.
     */
    private function readPropertyValue(string $path): mixed
    {
        $normalizedPath = $this->normalizePath($path);

        $value = $this;
        $keys = [[static::class, $this]];
        foreach ($normalizedPath as $key) {
            $keys[] = [$key, $value];

            if (is_array($value)) {
                if (array_key_exists($key, $value)) {
                    $value = $value[$key];
                    continue;
                }
                throw new UndefinedArrayElementException($this->makePropertyPathString($keys));
            }

            if (is_object($value)) {
                $class = new ReflectionClass($value);
                try {
                    $property = $class->getProperty($key);
                } catch (ReflectionException) {
                    throw new UndefinedObjectPropertyException($this->makePropertyPathString($keys));
                }
                if ($property->isStatic()) {
                    throw new StaticObjectPropertyException($this->makePropertyPathString($keys));
                }
                $value = $property->getValue($value);
                continue;
            }

            array_pop($keys);
            throw new PropertyNotSupportNestedValuesException($this->makePropertyPathString($keys), $value);
        }

        return $value;
    }

    /**
     * Return a meta information for a property at a given path.
     *
     * @param int $metaKey Determines which meta information to return. One of `FormModel::META_*` constants.
     * @param string $path Property path.
     * @return ?string Meta information for a property.
     *
     * @psalm-param self::META_* $metaKey
     */
    private function readPropertyMetaValue(int $metaKey, string $path): ?string
    {
        $normalizedPath = $this->normalizePath($path);

        $value = $this;
        $n = 0;
        foreach ($normalizedPath as $key) {
            if ($value instanceof FormModelInterface) {
                $nestedProperty = implode('.', array_slice($normalizedPath, $n));
                $data = match ($metaKey) {
                    self::META_LABEL => $value->getPropertyLabels(),
                    self::META_HINT => $value->getPropertyHints(),
                    self::META_PLACEHOLDER => $value->getPropertyPlaceholders(),
                };
                if (array_key_exists($nestedProperty, $data)) {
                    return $data[$nestedProperty];
                }
            }

            $class = new ReflectionClass($value);
            try {
                $property = $class->getProperty($key);
            } catch (ReflectionException) {
                return null;
            }
            if ($property->isStatic()) {
                return null;
            }

            $valueByAttribute = $this->getPropertyMetaValueByAttribute($metaKey, $property);
            if ($valueByAttribute!== null) {
                return $valueByAttribute;
            }

            $value = $property->getValue($value);
            if (!is_object($value)) {
                return null;
            }

            $n++;
        }

        return null;
    }

    /**
     * Generates a user-friendly property label based on the given property name.
     *
     * This is done by replacing underscores, dashes and dots with blanks and changing the first letter of each word to
     * upper case.
     *
     * For example, 'department_name' or 'DepartmentName' will generate 'Department Name'.
     *
     * @param string $property The property name.
     *
     * @return string The property label.
     */
    private function generatePropertyLabel(string $property): string
    {
        if (self::$inflector === null) {
            self::$inflector = new Inflector();
        }

        return StringHelper::uppercaseFirstCharacterInEachWord(
            self::$inflector->toWords($property)
        );
    }

    /**
     * Normalize property path and return it as an array.
     *
     * @return string[] Normalized property path as an array.
     */
    private function normalizePath(string $path): array
    {
        $path = str_replace(['][', '['], '.', rtrim($path, ']'));
        return StringHelper::parsePath($path);
    }

    /**
     * Convert array property path to its string representation.
     *
     * @param array $keys Property path as an array.     *
     * @psalm-param array<array-key, array{0:int|string, 1:mixed}> $keys
     * @return string Property path as string.
     */
    private function makePropertyPathString(array $keys): string
    {
        $path = '';
        foreach ($keys as $key) {
            if ($path !== '') {
                if (is_object($key[1])) {
                    $path .= '::$' . $key[0];
                } elseif (is_array($key[1])) {
                    $path .= '[' . $key[0] . ']';
                }
            } else {
                $path = (string) $key[0];
            }
        }
        return $path;
    }

    /**
     * @psalm-param self::META_* $metaKey
     */
    private function getPropertyMetaValueByAttribute(int $metaKey, ReflectionProperty $property): ?string
    {
        switch ($metaKey) {
            /** Try to get label from {@see Label} PHP attribute. */
            case self::META_LABEL:
                $attributes = $property->getAttributes(Label::class, ReflectionAttribute::IS_INSTANCEOF);
                if (!empty($attributes)) {
                    /** @var Label $instance */
                    $instance = $attributes[0]->newInstance();

                    return $instance->getLabel();
                }

                break;
            /** Try to get label from {@see Hint} PHP attribute. */
            case self::META_HINT:
                $attributes = $property->getAttributes(Hint::class, ReflectionAttribute::IS_INSTANCEOF);
                if (!empty($attributes)) {
                    /** @var Hint $instance */
                    $instance = $attributes[0]->newInstance();

                    return $instance->getHint();
                }

                break;
            /** Try to get label from {@see Placeholder} PHP attribute. */
            case self::META_PLACEHOLDER:
                $attributes = $property->getAttributes(Placeholder::class, ReflectionAttribute::IS_INSTANCEOF);
                if (!empty($attributes)) {
                    /** @var Placeholder $instance */
                    $instance = $attributes[0]->newInstance();

                    return $instance->getPlaceholder();
                }

                break;
        }

        return null;
    }
}
