<?php

namespace Mpietrucha\Support\Utilizer\Concerns;

trait Utilizable
{
    protected static mixed $utilizable = null;

    public static function use(): void
    {
        func_get_arg(0) |> static::utilizable(...);
    }

    public static function utilize(): mixed
    {
        return static::$utilizable ??= static::hydrate();
    }

    protected static function utilizable(mixed $utilizable): void
    {
        static::$utilizable = $utilizable;
    }

    protected static function hydrate(): mixed
    {
        return null;
    }
}
