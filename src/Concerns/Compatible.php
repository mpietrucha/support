<?php

namespace Mpietrucha\Support\Concerns;

trait Compatible
{
    public static function compatible(mixed ...$arguments): bool
    {
        /** @phpstan-ignore argument.type */
        return (bool) static::compatibility(...$arguments);
    }

    final public static function incompatible(mixed ...$arguments): bool
    {
        return ! static::compatible(...$arguments);
    }

    protected static function compatibility(): mixed
    {
        return false;
    }
}
