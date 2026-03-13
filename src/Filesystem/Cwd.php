<?php

namespace Mpietrucha\Support\Filesystem;

use Mpietrucha\Support\Concerns\Utilizable;
use Mpietrucha\Support\Filesystem;

abstract class Cwd
{
    use Utilizable\Strings;

    public static function get(): string
    {
        return static::utilize();
    }

    protected static function hydrate(): string
    {
        $cwd = Filesystem::cwd();

        if (Path::build('composer.json', $cwd) |> Filesystem::exists(...)) {
            return $cwd;
        }

        return Path::directory($cwd);
    }
}
