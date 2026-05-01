<?php

namespace Mpietrucha\Support;

use Mpietrucha\Support\Filesystem\Path as FilesystemPath;

abstract class ClassNamespace
{
    public static function delimiter(): string
    {
        return Str::backslash();
    }

    public static function join(string ...$elements): string
    {
        return FilesystemPath::join(...$elements) |> static::transform(...);
    }

    public static function canonicalize(string $namespace): string
    {
        return static::join(static::delimiter(), $namespace);
    }

    public static function name(string $namespace): string
    {
        return static::normalize($namespace) |> FilesystemPath::name(...);
    }

    public static function parent(string $namespace, ?int $level = null): string
    {
        return FilesystemPath::directory(static::normalize($namespace), $level) |> static::transform(...);
    }

    protected static function normalize(string $namespace): string
    {
        return Str::replace(static::delimiter(), FilesystemPath::delimiter(), $namespace);
    }

    protected static function transform(string $namespace): string
    {
        return Str::replace(FilesystemPath::delimiter(), static::delimiter(), $namespace);
    }
}
