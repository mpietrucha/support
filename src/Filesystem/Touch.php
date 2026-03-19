<?php

namespace Mpietrucha\Support\Filesystem;

use Illuminate\Support\Stringable;
use Mpietrucha\Support\Filesystem;

abstract class Touch
{
    public static function file(string $path, ?string $directory = null): string
    {
        $stringableFile = static::build($path, $directory);

        Path::directory(...) |> $stringableFile->pipe(...) |> static::directory(...);

        /** @phpstan-ignore argument.type */
        return Filesystem::touch(...) |> $stringableFile->tap(...) |> static::normalize(...);
    }

    public static function directory(string $path, ?string $directory = null): string
    {
        /** @phpstan-ignore argument.type */
        return Filesystem::ensureDirectoryExists(...) |> static::build($path, $directory)->tap(...) |> static::normalize(...);
    }

    protected static function normalize(string $path): string
    {
        return Path::get($path);
    }

    protected static function build(string $path, ?string $directory = null): Stringable
    {
        return Path::build($path, $directory) |> str(...);
    }
}
