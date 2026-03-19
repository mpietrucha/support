<?php

namespace Mpietrucha\Support\Exception\Concerns;

use Closure;
use Illuminate\Support\Collection;
use Mpietrucha\Support\Backtrace;
use Mpietrucha\Support\Backtrace\Frame;
use Mpietrucha\Support\Concerns\Creatable;
use Mpietrucha\Support\Exception\Builder;
use Mpietrucha\Support\Instance;
use Mpietrucha\Support\Reflection\ReflectionThrowable;
use Throwable;

/**
 * @phpstan-require-implements Throwable
 */
trait InteractsWithThrowable
{
    use Creatable;

    protected static ?Closure $buildUsing = null;

    /**
     * @param  Closure(Builder): void  $buildUsing
     */
    public static function buildUsing(Closure $buildUsing): void
    {
        static::$buildUsing = $buildUsing;
    }

    public static function build(?string $message = null, null|bool|float|int|string ...$arguments): static
    {
        $builder = Builder::make();

        $tap = tap(static::$buildUsing, static function (): void {
            static::$buildUsing = null;
        });

        return static::create(...$builder->build($tap, $message, ...$arguments));
    }

    public static function make(?string $message = null, null|bool|float|int|string ...$arguments): static
    {
        $backtrace = Backtrace::get(DEBUG_BACKTRACE_IGNORE_ARGS);

        $backtrace = $backtrace->pipeThrough([
            static fn (Collection $backtrace) => $backtrace->takeWhile(static function (Frame $frame): bool {
                $class = $frame->getClass();

                return $class === null || Instance::traits($class)->contains(__TRAIT__);
            }),
            static fn (Collection $backtrace): int => $backtrace->count() - 1,
        ]) |> $backtrace->skip(...) /** @phpstan-ignore argument.type */;

        $exception = static::build($message, ...$arguments);

        if ($backtrace->isEmpty()) {
            return $exception;
        }

        $reflectionThrowable = ReflectionThrowable::make($exception);

        /** @var Frame $frame */
        $frame = $backtrace->shift();

        $reflectionThrowable->getLineProperty()->setValue(
            $exception,
            $frame->getLine()
        );

        $reflectionThrowable->getFileProperty()->setValue(
            $exception,
            $frame->getFile()
        );

        $reflectionThrowable->getTraceProperty()->setValue(
            $exception,
            $backtrace->toArray()
        );

        return $exception;
    }

    public static function throw(?string $message = null, null|bool|float|int|string ...$arguments): never
    {
        throw static::make($message, ...$arguments);
    }
}
