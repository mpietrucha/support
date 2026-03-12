<?php

namespace Mpietrucha\Support;

use Illuminate\Support\Collection;
use Mpietrucha\Support\Exception\RuntimeException;

abstract class Instance
{
    /**
     * @return class-string
     */
    public static function namespace(object|string $instance): string
    {
        if (is_object($instance)) {
            return get_class($instance);
        }

        if (! class_exists($instance)) {
            RuntimeException::throw('Class %s not found', $instance);
        }

        return $instance;
    }

    /**
     * @return class-string
     */
    public static function base(object|string $instance): string
    {
        $namespace = static::namespace($instance);

        while ($base = get_parent_class($namespace)) {
            $namespace = $base;
        }

        return $namespace;
    }

    /**
     * @return Collection<string, string>
     */
    public static function traits(object|string $instance): Collection
    {
        return class_uses_recursive($instance) |> collect(...);
    }
}
