<?php

namespace Mpietrucha\Support;

use Closure;
use Composer\Autoload\ClassLoader;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Mpietrucha\Support\Exception\RuntimeException;
use Mpietrucha\Support\Filesystem\Path;
use Mpietrucha\Support\Instance\BindExceptionHandler;
use Mpietrucha\Support\Instance\SerializableInstance;
use Mpietrucha\Support\Reflection\ReflectionClosure;

/**
 * @phpstan-type TraitCollection Collection<class-string, class-string>
 */
abstract class Instance
{
    /**
     * @return ($class is object ? class-string : null|class-string)
     */
    public static function namespace(object|string $class, bool $autoload = true): ?string
    {
        if (is_object($class)) {
            return $class::class;
        }

        return class_exists($class, $autoload) ? $class : null;
    }

    public static function file(object|string $class): ?string
    {
        if (is_object($class)) {
            $class = static::namespace($class);
        }

        /** @var null|string */
        $file = Arr::map(
            ClassLoader::getRegisteredLoaders(),
            static fn (ClassLoader $classLoader): ?string => $classLoader->findFile($class) ?: null
        ) |> Arr::whereNotNull(...) |> Arr::first(...);

        if ($file === null) {
            return null;
        }

        return Path::canonicalize($file);
    }

    /**
     * @return ($class is object ? class-string : null|class-string)
     */
    public static function base(object|string $class): ?string
    {
        $class = static::namespace($class);

        if ($class === null) {
            return null;
        }

        while ($base = get_parent_class($class)) {
            $class = $base;
        }

        return $class;
    }

    public static function serialize(object $instance): string
    {
        return SerializableInstance::make($instance) |> serialize(...);
    }

    public static function unserialize(string $data): object
    {
        $instance = unserialize($data);

        if (! is_object($instance)) {
            RuntimeException::throw('Unable to unserialize the given data into an object');
        }

        /** @var object */
        return match (true) {
            $instance instanceof SerializableInstance => $instance(),
            default => $instance
        };
    }

    /**
     * @param  null|object|class-string  $scope
     * @param  null|object|class-string  $source
     */
    public static function bind(Closure $closure, ?object $context = null, null|object|string $scope = null, null|object|string $source = null): Closure
    {
        /** @var object|null|class-string $scope */
        $scope = match (true) {
            $context === null => $scope,
            $scope === null => static::namespace($context),
            default => $scope,
        };

        $reflectionClosure = ReflectionClosure::make($closure);

        if ($reflectionClosure->isStatic()) {
            $context = null;
        }

        if ($reflectionClosure->isUnbound()) {
            return $closure->bindTo($context, $scope);
        }

        BindExceptionHandler::use($reflectionClosure, $source);

        /** @var Closure $unbound */
        $unbound = static::serialize($closure) |> static::unserialize(...);

        if ($scope === null && $context === null) {
            return $unbound;
        }

        return $unbound->bindTo($context, $scope);
    }

    /**
     * @return TraitCollection
     */
    public static function traits(object|string $class): Collection
    {
        /** @var TraitCollection */
        return @class_uses_recursive($class) |> collect(...);
    }
}
