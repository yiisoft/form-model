<?php

declare(strict_types=1);

namespace Yiisoft\FormModel;

use Yiisoft\Strings\StringHelper;

/**
 * @internal
 */
final class PathNormalizer
{
    /**
     * Normalize property path and return it as an array.
     *
     * @return string[] Normalized property path as an array.
     *
     * @psalm-return non-empty-list<string>
     */
    public static function normalize(string $path): array
    {
        $path = str_replace(['][', '['], '.', rtrim($path, ']'));
        return StringHelper::parsePath($path);
    }
}
