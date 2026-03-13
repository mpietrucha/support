<?php

namespace Mpietrucha\Support\Filesystem;

use Symfony\Component\Filesystem\Path;

abstract class Extension
{
    public static function exists(string $path): bool
    {
        return Path::hasExtension($path);
    }

    final public static function unexists(string $path): bool
    {
        return ! static::exists($path);
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
