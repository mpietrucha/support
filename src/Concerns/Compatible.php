<?php

namespace Mpietrucha\Support\Concerns;

trait Compatible
{
    public static function compatible(mixed ...$arguments): bool
    {
        return false;
    }

    final public static function incompatible(mixed ...$arguments): bool
    {
        /** @phpstan-ignore argument.type */
        return ! static::compatible(...$arguments);
    }
}
