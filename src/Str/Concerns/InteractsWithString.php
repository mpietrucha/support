<?php

declare(strict_types=1);

namespace Mpietrucha\Support\Str\Concerns;

use Mpietrucha\Support\Str;
use Mpietrucha\Support\Stubs\StubRenderer;

/**
 * @phpstan-import-type StubReplacements from StubRenderer
 */
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
     * @param  StubReplacements  $replacements
     */
    public static function stub(string $value, array $replacements, ?string $prefix = null, ?string $suffix = null): string
    {
        return StubRenderer::render($value, $replacements, $prefix, $suffix);
    }

    public static function relationshipName(string $attribute): ?string
    {
        $indicator = static::dot();

        $relationship = Str::beforeLast($attribute, $indicator);

        if ($relationship === $attribute) {
            return null;
        }

        return static::nullWhenEmpty($relationship);
    }

    public static function relationshipAttribute(string $attribute): string
    {
        $indicator = static::dot();

        return Str::afterLast($attribute, $indicator);
    }
}
