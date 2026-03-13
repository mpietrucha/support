<?php

namespace Mpietrucha\Support\Filesystem\Temporary;

use Illuminate\Support\Str;
use Mpietrucha\Support\Concerns\Compatible;
use Mpietrucha\Support\Filesystem\Extension;
use Mpietrucha\Support\Filesystem\Path;

abstract class Name
{
    use Compatible;

    public static function delimiter(): string
    {
        return '-';
    }

    public static function default(): string
    {
        return Str::random(32);
    }

    public static function get(?string $name = null, bool $unique = false): string
    {
        $name = static::normalize($name);

        if ($unique) {
            $name = $name . static::default();
        }

        return $name . static::delimiter() . static::hash($name);
    }

    protected static function compatibility(string $name): bool
    {
        $name = static::normalize($name);

        if (Extension::exists($name)) {
            return false;
        }

        $signature = static::delimiter() |> str($name)->explode(...);

        return $signature->last() === $signature->firstOrFail() |> static::hash(...);
    }

    protected static function hash(string $value): string
    {
        return md5($value);
    }

    protected static function normalize(?string $name = null): string
    {
        $name ??= static::default();

        return Path::name($name);
    }
}
