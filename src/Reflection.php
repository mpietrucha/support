<?php

namespace Mpietrucha\Support;

use Mpietrucha\Support\Concerns\Makeable;
use ReflectionClass;

/**
 * @extends ReflectionClass<object>
 */
class Reflection extends ReflectionClass
{
    use Makeable;

    public static function base(object|string $instance): static
    {
        return Instance::base($instance) |> static::make(...);
    }
}
