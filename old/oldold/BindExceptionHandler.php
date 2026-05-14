<?php

namespace Mpietrucha\Support\Instance;

use Illuminate\Support\Arr;
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
 * @phpstan-type BindingCollection Collection<int, Binding>
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
            static::transformMessage($throwable)
        );

        $reflection->getLineProperty()->setValue(
            $throwable,
            static::transformLine($throwable)
        );

        $reflection->getFileProperty()->setValue(
            $throwable,
            static::transformFile($throwable)
        );

        $reflection->getTraceProperty()->setValue(
            $throwable,
            Backtrace::throwable($throwable)->map(static function (Frame $frame): FrameBuilder {
                $line = static::transformLine($frame);
                $file = static::transformFile($frame);
                $function = static::transformFunction($frame);

                return Frame::build($frame)->setLine($line)->setFile($file)->setFunction($function);
            })->toArray()
        );
    }

    protected static function transformMessage(Throwable $throwable): string
    {
        $bindings = static::bindings($message = $throwable->getMessage());

        if ($bindings->isEmpty()) {
            return $message;
        }

        return static::reduce($bindings, $message);
    }

    protected static function transformFunction(Frame $frame): string
    {
        $bindings = static::bindings($function = $frame->getFunction());

        if ($bindings->isEmpty()) {
            return $function;
        }

        return static::reduce($bindings, $function);
    }

    protected static function transformLine(Frame|Throwable $frameOrInput): ?int
    {
        $line = $frameOrInput->getLine();

        if ($line === null) {
            return null;
        }

        $bindings = $frameOrInput->getFile() |> static::bindings(...);

        if ($bindings->isEmpty()) {
            return $line;
        }

        return $bindings->first()->transformLine($line);
    }

    protected static function transformFile(Frame|Throwable $frameOrInput): ?string
    {
        $bindings = static::bindings($file = $frameOrInput->getFile());

        if ($bindings->isEmpty()) {
            return $file;
        }

        /** @var string $file */
        return $bindings->first()->transformFile($file);
    }

    /**
     * @param  BindingCollection  $bindings
     */
    protected static function reduce(Collection $bindings, string $value): string
    {
        return $bindings->reduce(static function (string $value, Binding $binding): string {
            return $binding->transform($value);
        }, $value);
    }

    /**
     * @return BindingCollection
     */
    protected static function bindings(mixed $value): Collection
    {
        $bindings = Arr::where(
            static::$bindings,
            static fn (array $bindings): bool => Binding::compatible($value, $bindings[0])
        );

        return Binding::make(...) |> collect($bindings)->mapSpread(...);
    }
}
