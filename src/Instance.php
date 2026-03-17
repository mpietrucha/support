<?php

namespace Mpietrucha\Support;

use Closure;
use Composer\Autoload\ClassLoader;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Laravel\SerializableClosure\Support\ClosureStream;
use Mpietrucha\Support\Backtrace\Frame;
use Mpietrucha\Support\Exception\RuntimeException;
use Mpietrucha\Support\Filesystem\Path;
use Mpietrucha\Support\Instance\SerializableInstance;
use Mpietrucha\Support\Reflection\ReflectionClosure;
use Mpietrucha\Support\Reflection\ReflectionThrowable;
use Throwable;

abstract class Instance
{
    /**
     * @return ($class is object ? class-string : null|class-string)
     */
    public static function namespace(object|string $class, bool $autoload = true): ?string
    {
        if (is_object($class)) {
            return get_class($class);
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
            fn (ClassLoader $loader) => $loader->findFile($class) ?: null
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
        /** @var Closure $unbound */
        $unbound = static::serialize($closure) |> static::unserialize(...);

        /** @var object|null|class-string $scope */
        $scope = match (true) {
            $context === null => $scope,
            $scope === null => static::namespace($context),
            default => $scope
        };

        if ($scope === null && $context === null) {
            return $unbound;
        }

        $bound = $unbound->bindTo($context, $scope);

        return function (mixed ...$arguments) use ($bound, $closure, $source) {
            try {
                return $bound(...$arguments);
            } catch (Throwable $exception) {
                $closure = ReflectionClosure::make($closure);

                $source = match (true) {
                    $source === null => $closure->getClosureScopeClass(),
                    default => Reflection::make($source)
                };

                $file = $source?->getFileName() ?? $closure->getFileName();

                $line = $source?->getMethod(
                    $closure->getName()
                )->getStartLine() ?? $closure->getStartLine();

                $indicator = ClosureStream::STREAM_PROTO;

                $reflection = ReflectionThrowable::make($exception);

                $reflection->getLineProperty()->setValue(
                    $exception,
                    (function () use ($exception, $indicator, $line, $source) {
                        $value = $exception->getLine();

                        if (Str::doesntContain($exception->getMessage(), $indicator)) {
                            return $value;
                        }

                        if ($source === null) {
                            return $line;
                        }

                        return $line + $value - 2;
                    })()
                );

                $reflection->getMessageProperty()->setValue(
                    $exception,
                    (function () use ($exception, $indicator, $file, $line) {
                        $value = $exception->getMessage();

                        if (Str::doesntContain($value, $indicator)) {
                            return $value;
                        }

                        $code = Str::between($value, 'closure:', '}');

                        return Str::replace($code, sprintf('%s:%s', $file, $line), $value);
                    })()
                );

                $reflection->getFileProperty()->setValue(
                    $exception,
                    (function () use ($exception, $indicator, $file) {
                        $value = $exception->getFile();

                        return Str::contains($value, $indicator) ? $file : $value;
                    })()
                );

                $reflection->getTraceProperty()->setValue(
                    $exception,
                    Backtrace::throwable($exception)->map(function (Frame $frame) use ($indicator, $file, $line) {
                        $function = $frame->function();

                        if (Str::doesntContain($function, $indicator)) {
                            return $frame;
                        }

                        return sprintf('{closure:%s:%s}', $file, $line) |> Frame::build($frame)->function(...);
                    })->toArray()
                );

                throw $exception;
            }
        };
    }

    /**
     * @return Collection<string, string>
     */
    public static function traits(object|string $class): Collection
    {
        return @class_uses_recursive($class) |> collect(...);
    }
}
