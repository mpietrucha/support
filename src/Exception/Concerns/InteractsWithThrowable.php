<?php

namespace Mpietrucha\Support\Exception\Concerns;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Mpietrucha\Support\Concerns\Creatable;
use Mpietrucha\Support\Exception\PendingException;
use Mpietrucha\Support\Instance;
use Mpietrucha\Support\Reflection;

/**
 * @phpstan-require-implements \Throwable
 */
trait InteractsWithThrowable
{
    use Creatable;

    protected static ?Closure $configurator = null;

    public static function configure(Closure $configurator): void
    {
        static::$configurator = $configurator;
    }

    public static function build(string $message): static
    {
        $configurator = static::configurator();

        if ($configurator === null) {
            return static::create($message);
        }

        return static::create($message, ...PendingException::run($configurator));
    }

    public static function make(string $message, null|bool|float|int|string ...$arguments): static
    {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS) |> collect(...);

        $backtrace = $backtrace->pipeThrough([
            fn (Collection $backtrace) => $backtrace->takeWhile(function (array $frame) {
                $class = Arr::string($frame, 'class');

                return Instance::traits($class)->contains(__TRAIT__);
            }),
            fn (Collection $backtrace) => $backtrace->count() - 1,
        ]) |> $backtrace->skip(...) /** @phpstan-ignore argument.type */;

        $exception = sprintf($message, ...$arguments) |> static::build(...);

        $reflection = Reflection::base($exception);

        $reflection->getProperty($property = 'line')->setValue(
            $exception,
            $backtrace->value($property)
        );

        $reflection->getProperty($property = 'file')->setValue(
            $exception,
            $backtrace->value($property)
        );

        $reflection->getProperty('trace')->setValue(
            $exception,
            $backtrace->skip(1)->all()
        );

        return $exception;
    }

    public static function throw(string $message, null|bool|float|int|string ...$arguments): never
    {
        throw static::make($message, ...$arguments);
    }

    protected static function configurator(): ?Closure
    {
        $configurator = static::$configurator;

        static::$configurator = null;

        return $configurator;
    }
}
