<?php

namespace Mpietrucha\Support;

class Types
{
    public static function __callStatic(string $method, array $arguments): bool
    {
        $method = str($method)->snake()->prepend('is_')->toString();

        return $method(...$arguments);
    }
}
