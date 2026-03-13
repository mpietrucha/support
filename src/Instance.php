<?php

namespace Mpietrucha\Support;

use Closure;
use Composer\Autoload\ClassLoader;
use Illuminate\Support\Collection;
use Mpietrucha\Support\Exception\RuntimeException;
use Mpietrucha\Support\Instance\Serializable;

abstract class Instance
{
    /**
     * @return ($instance is object ? class-string : null|class-string)
     */
    public static function namespace(object|string $instance, bool $autoload = true): ?string
    {
        if (is_object($instance)) {
            return get_class($instance);
        }

        if (! class_exists($instance, $autoload)) {
            return null;
        }

        return $instance;
    }

    public static function file(object|string $instance, bool $autoload = false): ?string
    {
        $namespace = match (true) {
            is_string($instance) => $instance,
            default => static::namespace($instance)
        };

        $loaders = ClassLoader::getRegisteredLoaders() |> collect(...);

        $file = $loaders
            ->map
            ->findFile($namespace)
            ->first();

        if (is_string($file)) {
            return $file;
        }

        if (! class_exists($namespace, $autoload)) {
            return null;
        }

        return Reflection::make($instance)->getFileName() ?: null;
    }

    /**
     * @return null|class-string
     */
    public static function base(object|string $instance): ?string
    {
        $namespace = static::namespace($instance);

        if ($namespace === null) {
            return null;
        }

        while ($base = get_parent_class($namespace)) {
            $namespace = $base;
        }

        return $namespace;
    }

    public static function serialize(callable|object $instance): string
    {
        $serializable = Serializable::make($instance);

        return serialize($serializable);
    }

    public static function unserialize(string $instance): object
    {
        $instance = unserialize($instance);

        if (! is_object($instance)) {
            RuntimeException::throw('Unserialized data is not an object');
        }

        /** @var object */
        return match (true) {
            $instance instanceof Serializable => $instance(),
            default => $instance
        };
    }

    public static function bind(callable $callable, ?object $context = null, null|object|string $scope = null): Closure
    {
        /** @var Closure $closure */
        $closure = static::serialize($callable) |> static::unserialize(...);

        /** @var object|null|class-string $scope */
        $scope = match (true) {
            $context === null => $scope,
            $scope === null => static::namespace($context),
            default => $scope
        };

        return $closure->bindTo($context, $scope);
    }

    public static function hash(object $instance, string $algorithm = 'md5'): string
    {
        return hash($algorithm, static::serialize($instance));
    }

    /**
     * @return Collection<string, string>
     */
    public static function traits(object|string $instance): Collection
    {
        return class_uses_recursive($instance) |> collect(...);
    }
}
