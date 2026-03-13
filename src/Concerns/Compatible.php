<?php

namespace Mpietrucha\Support\Concerns;

use Mpietrucha\Support\Forward\Concerns\Forwardable;

trait Compatible
{
    use Forwardable;

    public static function compatible(mixed ...$arguments): bool
    {
        return (bool) static::forward(__CLASS__)->eval(__FUNCTION__, $arguments);
    }

    final public static function incompatible(mixed ...$arguments): bool
    {
        return ! static::forward(__CLASS__)->eval(__FUNCTION__, $arguments);
    }

    protected static function compatibility(): mixed
    {
        return false;
    }
}
