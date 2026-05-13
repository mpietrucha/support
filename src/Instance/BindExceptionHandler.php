<?php

namespace Mpietrucha\Support\Instance;

use Illuminate\Support\Arr;
use Mpietrucha\Support\Backtrace;
use Mpietrucha\Support\Backtrace\Frame;
use Mpietrucha\Support\Backtrace\FrameBuilder;
use Mpietrucha\Support\Reflection\ReflectionClosure;
use Mpietrucha\Support\Reflection\ReflectionThrowable;
use Throwable;

/**
 * @phpstan-import-type BindingSource from Binding
 *
 * @phpstan-type PendingBinding array{ReflectionClosure, BindingSource}
 */
abstract class BindExceptionHandler
{
    /**
     * @var array<int, PendingBinding>
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

        static::$bindings[] = [$closure, $source];
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

        $reflection = ReflectionThrowable::make($throwable);

        $reflection->getMessageProperty()->setValue(
            $throwable,
            static::getMessage($throwable)
        );

        $reflection->getLineProperty()->setValue(
            $throwable,
            static::getLine($throwable)
        );

        $reflection->getFileProperty()->setValue(
            $throwable,
            static::getFile($throwable)
        );

        $reflection->getTraceProperty()->setValue(
            $throwable,
            Backtrace::throwable($throwable)->map(static function (Frame $frame): FrameBuilder {
                $line = static::getLine($frame);
                $file = static::getFile($frame);
                $function = static::getFunction($frame);

                return Frame::build($frame)->setLine($line)->setFile($file)->setFunction($function);
            })->toArray()
        );
    }

    protected static function getMessage(Throwable $throwable): string
    {
        $binding = static::binding($message = $throwable->getMessage());

        if (! $binding instanceof Binding) {
            return $message;
        }

        return $binding->transformClosure($message);
    }

    protected static function getFunction(Frame $frame): string
    {
        $binding = static::binding($function = $frame->getFunction());

        if (! $binding instanceof Binding) {
            return $function;
        }

        return $binding->transformClosure($function);
    }

    protected static function getLine(Frame|Throwable $frameOrInput): ?int
    {
        $line = $frameOrInput->getLine();

        if ($line === null) {
            return null;
        }

        $binding = $frameOrInput->getFile() |> static::binding(...);

        if (! $binding instanceof Binding) {
            return $line;
        }

        return $binding->transformLine($line);
    }

    protected static function getFile(Frame|Throwable $frameOrInput): ?string
    {
        $binding = static::binding($file = $frameOrInput->getFile());

        if (! $binding instanceof Binding) {
            return $file;
        }

        /** @var string $file */
        return $binding->transformFile($file);
    }

    protected static function binding(mixed $value): ?Binding
    {
        $bindings = Arr::first(
            static::$bindings,
            static fn (array $bindings): bool => Binding::compatible($value, $bindings[0])
        );

        if ($bindings === null) {
            return null;
        }

        return Binding::make(...$bindings);
    }
}
