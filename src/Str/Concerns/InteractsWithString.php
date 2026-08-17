<?php

declare(strict_types=1);

namespace Mpietrucha\Support\Str\Concerns;

use Illuminate\Support\Str;

trait InteractsWithString
{
    public static function eol(): string
    {
        return PHP_EOL;
    }

    public static function none(): string
    {
        return '';
    }

    public static function tab(): string
    {
        return "\t";
    }

    public static function slash(): string
    {
        return '/';
    }

    public static function backslash(): string
    {
        return '\\';
    }

    public static function dot(): string
    {
        return '.';
    }

    public static function comma(): string
    {
        return ',';
    }

    public static function dash(): string
    {
        return '-';
    }

    public static function space(): string
    {
        return ' ';
    }

    public static function nullWhenEmpty(string $value): ?string
    {
        return $value === static::none() ? null : $value;
    }

    /**
     * @param  array<string, string>  $values
     */
    public static function stub(string $stub, array $values, string $prefix = '{{', string $suffix = '}}'): string
    {
        return collect($values)->reduce(static function (string $stub, string $value, string $name) use ($prefix, $suffix): string {
            $replacements = [
                sprintf('%s%s%s', $prefix, $name, $suffix),
                sprintf('%s %s %s', $prefix, $name, $suffix),
            ];

            return Str::replace($replacements, $value, $stub);
        }, $stub);
    }
}
