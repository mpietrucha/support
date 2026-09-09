<?php

namespace Mpietrucha\Support\Filesystem;

use Illuminate\Support\Stringable;
use Mpietrucha\Support\Filesystem;
use Mpietrucha\Support\Str;

abstract class Touch
{
    public static function file(string $path, ?string $directory = null): string
    {
        $file = static::build($path, $directory);

        Path::directory(...) |> $file->pipe(...) |> static::directory(...);

        return Filesystem::touch(...) |> $file->tap(...) |> static::normalize(...);
    }

    public static function directory(string $path, ?string $directory = null): string
    {
        return Filesystem::ensureDirectoryExists(...) |> static::build($path, $directory)->tap(...) |> static::normalize(...);
    }

    protected static function normalize(string $path): string
    {
        return Path::get($path);
    }

    protected static function build(string $path, ?string $directory = null): Stringable
    {
        return Path::build($path, $directory) |> Str::of(...);
    }
}
