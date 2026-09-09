<?php

declare(strict_types=1);

namespace Mpietrucha\Support\Str\Concerns;

use Illuminate\Support\Collection;
use Mpietrucha\Support\Str;
use Mpietrucha\Support\Stringable;
use Mpietrucha\Support\Stubs\StubRenderer;

/**
 * @phpstan-import-type StubReplacements from StubRenderer
 */
trait InteractsWithString
{
    public static function of(mixed $value): Stringable
    {
        return Stringable::make($value);
    }

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

    public static function replacePattern(string $pattern, mixed $replacement, string $value, ?string $indicator = null): ?string
    {
        /** @phpstan-ignore argument.type */
        $segments = explode($indicator ?? '*', $pattern) |> collect(...);

        $regex = sprintf(
            '/%s/m',
            $segments->map(static function (string $segment): string {
                $delimiter = Str::backslash();

                return preg_quote($segment, $delimiter);
            })->implode('(\S+)')
        );

        return Str::replaceMatches($regex, static function (array $matches) use ($replacement, $segments): string {
            $matches = collect($matches);

            $matches->shift();

            /** @phpstan-ignore-next-line */
            $replacements = value($replacement, ...$matches) |> collect(...);

            /** @var string $segment */
            $segment = $segments->shift();

            return $replacements->zip($segments)->reduce(static function (string $carry, Collection $pairs): string {
                $delimiter = static::none();

                /** @var string */
                return $pairs->prepend($carry)->join($delimiter);
            }, $segment);
        }, $value);
    }
}
