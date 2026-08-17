<?php

declare(strict_types=1);

namespace Mpietrucha\Support\Str\Concerns;

use Illuminate\Support\Arr;
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
     * @param  array<string, string>  $replacements
     */
    public static function stub(string $value, array $replacements, ?string $prefix = '{{ ', ?string $suffix = ' }}'): string
    {
        /** @var array<string, string> $replacements */
        $replacements = Arr::mapWithKeys($replacements, static function (string $value, string $key) use ($prefix, $suffix): array {
            if (is_string($prefix)) {
                $key = Str::start($key, $prefix);
            }

            if (is_string($suffix)) {
                $key = Str::finish($key, $suffix);
            }

            return [$key => $value];
        });

        return static::swap($replacements, $value);
    }
}
