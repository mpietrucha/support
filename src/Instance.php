<?php

namespace Mpietrucha\Support;

use Closure;
use Composer\Autoload\ClassLoader;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Mpietrucha\Support\Exception\RuntimeException;
use Mpietrucha\Support\Filesystem\Path;
use Mpietrucha\Support\Instance\SerializableInstance;

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

        return class_exists($instance, $autoload) ? $instance : null;
    }

    public static function file(object|string $instance): ?string
    {
        if (is_object($instance)) {
            $instance = static::namespace($instance);
        }

        /** @var null|string */
        $file = Arr::map(
            ClassLoader::getRegisteredLoaders(),
            fn (ClassLoader $loader) => $loader->findFile($instance) ?: null
        ) |> Arr::whereNotNull(...) |> Arr::first(...);

        if ($file === null) {
            return null;
        }

        return Path::canonicalize($file);
    }

    /**
     * @return ($instance is object ? class-string : null|class-string)
     */
    public static function base(object|string $instance): ?string
    {
        $base = static::namespace($instance);

        if ($base === null) {
            return null;
        }

        while ($parent = get_parent_class($base)) {
            $base = $parent;
        }

        return $base;
    }

    public static function serialize(object $instance): string
    {
        return SerializableInstance::make($instance) |> serialize(...);
    }

    public static function unserialize(string $instance): object
    {
        $instance = unserialize($instance);

        if (! is_object($instance)) {
            RuntimeException::throw('Unable to unserialize the given data into an object');
        }

        /** @var object */
        return match (true) {
            $instance instanceof SerializableInstance => $instance(),
            default => $instance
        };
    }

    public static function bind(Closure $closure, ?object $context = null, null|object|string $scope = null): Closure
    {
        /** @var Closure $closure */
        $closure = static::serialize($closure) |> static::unserialize(...);

        /** @var object|null|class-string $scope */
        $scope = match (true) {
            $context === null => $scope,
            $scope === null => static::namespace($context),
            default => $scope
        };

        return $closure->bindTo($context, $scope);
    }

    /**
     * @return Collection<string, string>
     */
    public static function traits(object|string $instance): Collection
    {
        return class_uses_recursive($instance) |> collect(...);
    }
}
