<?php

namespace Mpietrucha\Support\Filesystem;

use Mpietrucha\Support\Filesystem\Concerns\InteractsWithExistence;
use Symfony\Component\Filesystem\Path;

abstract class Extension
{
    use InteractsWithExistence;

    public static function exists(string $path): bool
    {
        return Path::hasExtension($path);
    }

    public static function get(string $path): ?string
    {
        if (static::unexists($path)) {
            return null;
        }

        return Path::getExtension($path);
    }

    public static function set(string $path, string $extension): string
    {
        return Path::changeExtension($path, $extension);
    }
}
