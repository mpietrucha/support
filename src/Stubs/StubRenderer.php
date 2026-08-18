<?php

namespace Mpietrucha\Support\Stubs;

use Mpietrucha\Support\Filesystem;
use Mpietrucha\Support\Str;

/**
 * @phpstan-type StubReplacements array<string, string>
 */
class StubRenderer
{
    public static function getDefaultPrefix(): string
    {
        return '{{';
    }

    public static function getDefaultSuffix(): string
    {
        return '}}';
    }

    /**
     * @param  StubReplacements  $replacements
     */
    public static function render(string $value, array $replacements, ?string $prefix = null, ?string $suffix = null): string
    {
        $prefix ??= static::getDefaultPrefix();
        $suffix ??= static::getDefaultSuffix();

        return collect($replacements)->reduce(static function (string $value, string $replacement, string $name) use ($prefix, $suffix): string {
            $replacements = [
                sprintf('%s%s%s', $prefix, $name, $suffix),
                sprintf('%s %s %s', $prefix, $name, $suffix),
            ];

            return Str::replace($replacements, $replacement, $value);
        }, $value);
    }

    /**
     * @param  StubReplacements  $replacements
     */
    public static function file(string $path, array $replacements, ?string $prefix = null, ?string $suffix = null): string
    {
        $value = Filesystem::get($path);

        return static::render($value, $replacements, $prefix, $suffix);
    }
}
