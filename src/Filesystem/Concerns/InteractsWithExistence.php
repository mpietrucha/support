<?php

namespace Mpietrucha\Support\Filesystem\Concerns;

use Mpietrucha\Support\Filesystem;

/**
 * @internal
 */
trait InteractsWithExistence
{
    public static function exists(string $path): bool
    {
        return Filesystem::adapter()->exists($path);
    }

    final public static function unexists(string $path): bool
    {
        return ! static::exists($path);
    }
}
