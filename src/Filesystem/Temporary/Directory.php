<?php

namespace Mpietrucha\Support\Filesystem\Temporary;

use Mpietrucha\Support\Filesystem\Touch;

abstract class Directory
{
    /**
     * Get the default system temporary directory path.
     */
    public static function default(): string
    {
        return sys_get_temp_dir();
    }

    /**
     * Generate a temporary directory name.
     */
    public static function name(string $name): string
    {
        return Name::get($name);
    }

    /**
     * Get or create a temporary directory with the given name.
     */
    public static function get(string $name): string
    {
        $name = static::name($name);

        return Touch::directory($name, static::default());
    }
}
