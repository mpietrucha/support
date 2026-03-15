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

    public static function base(object|string $class): static
    {
        $instance = Instance::base($class) ?? $class;

        return static::make($class);
    }
}
