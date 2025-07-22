<?php

declare(strict_types=1);

namespace Yiisoft\FormModel;

use InvalidArgumentException;

/**
 * @internal
 *
 * This class parse a property expression and store real property name, prefix and suffix.
 *
 * A property expression is a property name prefixed and/or suffixed with array indexes. It is mainly used in
 * tabular data input and/or input of array type. Below are some examples:
 *
 * - `[0]content` is used in tabular data input to represent the "content" property for the first model in tabular
 *    input;
 * - `dates[0]` represents the first array element of the "dates" property;
 * - `[0]dates[0]` represents the first array element of the "dates" property for the first model in tabular
 *    input.
 */
final class ParsedProperty
{
    public readonly string $raw;
    public readonly string $name;
    public readonly string $prefix;
    public readonly string $suffix;

    /**
     * @psalm-var non-empty-list<string>
     */
    public readonly array $path;

    /**
     * @param string $property The property name or expression
     *
     * @throws InvalidArgumentException If the property name contains non-word characters.
     */
    public function __construct(string $property)
    {
        if (!preg_match('/(^|.*\])([\w\.\+\-_]+)(\[.*|$)/u', $property, $matches)) {
            throw new InvalidArgumentException('Property name must contain word characters only.');
        }
        $this->raw = $property;
        $this->name = $matches[2];
        $this->prefix = $matches[1];
        $this->suffix = $matches[3];
        $this->path = PathNormalizer::normalize($this->name . $this->suffix);
    }
}
