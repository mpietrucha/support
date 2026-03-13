<?php

namespace Mpietrucha\Support\Instance;

use Mpietrucha\Support\Filesystem\Path as FilesystemPath;
use Mpietrucha\Support\Str;

abstract class Path
{
    public static function delimiter(): string
    {
        return Str::backslash();
    }

    public static function join(string ...$elements): string
    {
        return FilesystemPath::join(...$elements) |> static::normalize(...);
    }

    public static function canonicalize(string $namespace): string
    {
        return static::join(static::delimiter(), $namespace);
    }

    public static function name(string $namespace): string
    {
        return FilesystemPath::normalize($namespace) |> FilesystemPath::name(...);
    }

    public static function namespace(string $namespace, ?int $level = null): string
    {
        return FilesystemPath::directory($namespace, $level) |> static::normalize(...);
    }

    protected static function normalize(string $namespace): string
    {
        return Str::replace(FilesystemPath::delimiter(), static::delimiter(), $namespace);
    }
}
