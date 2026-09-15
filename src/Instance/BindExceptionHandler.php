<?php

namespace Mpietrucha\Support\Instance;

use Illuminate\Support\Collection;
use Mpietrucha\Support\Backtrace;
use Mpietrucha\Support\Backtrace\Frame;
use Mpietrucha\Support\Backtrace\FrameBuilder;
use Mpietrucha\Support\Reflection\ReflectionClosure;
use Mpietrucha\Support\Reflection\ReflectionThrowable;
use Throwable;

/**
 * @phpstan-import-type BindingSource from Binding
 *
 * @phpstan-type BindingArguments array{ReflectionClosure, BindingSource}
 */
abstract class BindExceptionHandler
{
    /**
     * @var array<int, BindingArguments>
     */
    protected static array $bindings = [];

    protected static bool $disabled = false;

    protected static bool $initialized = false;

    protected static bool $shouldRegister = true;

    public static function disable(bool $disabled = true): void
    {
        static::$disabled = $disabled;
    }

    final public static function enable(bool $disabled = false): void
    {
        static::disable($disabled);
    }

    public static function shouldRegister(bool $shouldRegister = true): void
    {
        static::$shouldRegister = $shouldRegister;
    }

    final public static function dontRegister(bool $shouldRegister = false): void
    {
        static::shouldRegister($shouldRegister);
    }

    public static function flush(): void
    {
        static::$bindings = [];

        static::$initialized = false;
    }

    public static function initializeIfNotInitialized(): void
    {
        if (static::$disabled) {
            return;
        }

        if (static::$initialized) {
            return;
        }

        if (static::$shouldRegister) {
            static::handle(...) |> set_exception_handler(...);
        }

        static::$initialized = true;
    }

    /**
     * @param  BindingSource  $source
     */
    public static function use(ReflectionClosure $closure, null|object|string $source = null): void
    {
        if (static::$disabled) {
            return;
        }

        static::initializeIfNotInitialized();

        $exists = in_array($binding = [$closure, $source], static::$bindings);

        if ($exists) {
            return;
        }

        static::$bindings[] = $binding;
    }

    public static function handle(Throwable $throwable): void
    {
        static::transform($throwable);

        throw $throwable;
    }

    public static function transform(Throwable $throwable): void
    {
        if (static::$disabled) {
            return;
        }

        $reflectionThrowable = ReflectionThrowable::make($throwable);

        $reflectionThrowable->getMessageProperty()->setValue(
            $throwable,
            static::transformMessage($throwable)
        );

        $reflectionThrowable->getLineProperty()->setValue(
            $throwable,
            static::transformLine($throwable)
        );

        $reflectionThrowable->getFileProperty()->setValue(
            $throwable,
            static::transformFile($throwable)
        );

        $reflectionThrowable->getTraceProperty()->setValue(
            $throwable,
            Backtrace::throwable($throwable)->map(static function (Frame $frame): FrameBuilder {
                $line = static::transformLine($frame);
                $file = static::transformFile($frame);
                $function = static::transformFunction($frame);

                return Frame::build($frame)->setLine($line)->setFile($file)->setFunction($function);
            })->toArray()
        );
    }

    public static function transformMessage(Throwable $throwable): string
    {
        $bindings = static::getBindings($message = $throwable->getMessage());

        if ($bindings->isEmpty()) {
            return $message;
        }

        return $bindings->reduce(
            static fn (string $message, Binding $binding): string => $binding->transformMessage($message),
            $message
        );
    }

    public static function transformFunction(Frame $frame): string
    {
        $bindings = static::getBindings($function = $frame->getFunction());

        if ($bindings->isEmpty()) {
            return $function;
        }

        return $bindings->first()->transformFunction($function);
    }

    public static function transformLine(Frame|Throwable $frameOrInput): ?int
    {
        $line = $frameOrInput->getLine();

        if ($line === null) {
            return null;
        }

        $bindings = $frameOrInput->getFile() |> static::getBindings(...);

        if ($bindings->isEmpty()) {
            return $line;
        }

        return $bindings->first()->transformLine($line);
    }

    public static function transformFile(Frame|Throwable $frameOrInput): ?string
    {
        $bindings = static::getBindings($file = $frameOrInput->getFile());

        if ($bindings->isEmpty()) {
            return $file;
        }

        /** @var string $file */
        return $bindings->first()->transformFile($file);
    }

    /**
     * @return Collection<int, Binding>
     */
    protected static function getBindings(mixed $value): Collection
    {
        $bindings = static::$bindings |> Collection::make(...);

        $bindings->transform(static fn (array $arguments): ?Binding => Binding::for($value, ...$arguments));

        return $bindings->filter();
    }
}
