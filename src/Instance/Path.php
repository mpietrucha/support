<?php

namespace Mpietrucha\Support\Instance;

use Illuminate\Support\Str;
use Mpietrucha\Support\Filesystem\Path as Adapter;

abstract class Path
{
    public static function delimiter(): string
    {
        return '\\';
    }

    public static function join(string ...$elements): string
    {
        return Adapter::join(...$elements) |> static::normalize(...);
    }

    public static function canonicalize(string $namespace): string
    {
        $delimiter = static::delimiter();

        return static::join($delimiter, $namespace);
    }

    public static function name(string $namespace): string
    {
        return Adapter::normalize($namespace) |> Adapter::name(...);
    }

    public static function namespace(string $namespace, ?int $level = null): string
    {
        return Adapter::directory($namespace, $level) |> static::normalize(...);
    }

    protected static function normalize(string $namespace): string
    {
        $delimiter = Adapter::delimiter();

        return Str::replace($delimiter, static::delimiter(), $namespace);
    }
}
