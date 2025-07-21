<?php

declare(strict_types=1);

namespace Yiisoft\FormModel;

use InvalidArgumentException;

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
