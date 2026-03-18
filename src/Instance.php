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

                $reflection = ReflectionThrowable::make($exception);

                $indicator = ClosureStream::STREAM_PROTO;

                $reflection->getMessageProperty()->setValue(
                    $exception,
                    ($value = function (string $value) use ($indicator, $file, $line) {
                        if (Str::doesntContain($value, $indicator)) {
                            return $value;
                        }

                        if ($file === false) {
                            return $value;
                        }

                        $closure = Str::between($value, 'closure:', '}');

                        if ($closure === $value) {
                            return $value;
                        }

                        return Str::replace($closure, sprintf('%s::%s', $file, $line), $value);
                    })($exception->getMessage())
                );

                $reflection->getLineProperty()->setValue(
                    $exception,
                    ($line = function (?int $value, ?string $content) use ($indicator, $source, $line) {
                        if ($value === null) {
                            return null;
                        }

                        if (Str::doesntContain((string) $content, $indicator)) {
                            return $value;
                        }

                        if ($source === null) {
                            return $value;
                        }

                        return $value + $line - 2;
                    })($exception->getLine(), $exception->getMessage())
                );

                $reflection->getFileProperty()->setValue(
                    $exception,
                    ($file = function (?string $value) use ($file, $indicator) {
                        if ($value === null) {
                            return null;
                        }

                        if ($file === false) {
                            return $value;
                        }

                        return Str::contains($value, $indicator) ? $file : $value;
                    })($exception->getFile())
                );

                $reflection->getTraceProperty()->setValue(
                    $exception,
                    Backtrace::throwable($exception)->map(function (Frame $frame) use ($line, $file, $value) {
                        $line = $line($frame->getLine(), $frame->getFile());

                        $file = $frame->getFile() |> $file(...);

                        $function = $frame->getFunction() |> $value(...);

                        return Frame::build($frame)->setLine($line)->setFile($file)->setFunction($function);
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
