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
        $instance = Instance::base($instance) ?? $instance;

        return static::make($instance);
    }
}
