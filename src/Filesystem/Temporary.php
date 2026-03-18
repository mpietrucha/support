<?php

namespace Mpietrucha\Support\Filesystem;

use Mpietrucha\Support\Concerns\UtilizableStrings;
use Mpietrucha\Support\Filesystem;
use Mpietrucha\Support\Str;

abstract class Temporary
{
    use UtilizableStrings;

    public static function bucket(): string
    {
        return Path::build('mpietrucha-support-temporary-bucket', static::utilize());
    }

    public static function flush(): void
    {
        static::bucket() |> Filesystem::deleteDirectory(...);
    }

    public static function file(?string $name = null, ?string $directory = null): string
    {
        return static::path($name, $directory) |> Touch::file(...);
    }

    public static function directory(?string $name = null, ?string $directory = null): string
    {
        return static::path($name, $directory) |> Touch::directory(...);
    }

    public static function path(?string $name = null, ?string $directory = null): string
    {
        $path = Path::build(
            Path::join((string) $directory, $name ?? Str::random(32)),
            static::bucket()
        );

        if (is_string($name)) {
            return $path;
        }

        if (Filesystem::exists($path)) {
            return $path;
        }

        return static::path($name, $directory);
    }

    protected static function hydrate(): string
    {
        return sys_get_temp_dir();
    }
}
