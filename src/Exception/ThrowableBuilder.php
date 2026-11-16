<?php

namespace Mpietrucha\Support\Exception;

use Closure;
use Illuminate\Contracts\Support\Arrayable;
use Mpietrucha\Support\Concerns\Makeable;
use Mpietrucha\Support\Str;
use Throwable;

/**
 * @implements Arrayable<int, mixed>
 */
class ThrowableBuilder implements Arrayable
{
    use Makeable;

    protected ?string $message = null;

    protected int $code = 0;

    protected ?Throwable $previous = null;

    protected static ?Closure $tap = null;

    public static function tap(Closure $callback): void
    {
        if (! static::$tap instanceof Closure) {
            static::$tap = $callback;

            return;
        }

        $tap = static::$tap;

        static::$tap = static function (self $throwableBuilder) use ($tap, $callback): void {
            value($tap, $throwableBuilder);

            value($callback, $throwableBuilder);
        };
    }

    public static function useCode(int $code): void
    {
        static::tap(static function (self $throwableBuilder) use ($code): void {
            $throwableBuilder->setCode($code);
        });
    }

    public static function usePrevious(Throwable $throwable): void
    {
        static::tap(static function (self $throwableBuilder) use ($throwable): void {
            $throwableBuilder->setPrevious($throwable);
        });
    }

    public function setMessage(string $message, null|bool|float|int|string ...$arguments): static
    {
        $this->message = sprintf($message, ...$arguments);

        return $this;
    }

    public function setCode(int $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function setPrevious(Throwable $throwable): static
    {
        $this->previous = $throwable;

        return $this;
    }

    /**
     * @return array{string, int, null|Throwable}
     */
    public function toArray(): array
    {
        return [
            $this->message ?? Str::none(),
            $this->code,
            $this->previous,
        ];
    }

    /**
     * @template TThrowable of Throwable
     *
     * @param  class-string<TThrowable>  $throwable
     * @return TThrowable
     */
    public function build(string $throwable): Throwable
    {
        $tap = tap(static::$tap, static function (): void {
            static::$tap = null;
        });

        value($tap, $this);

        return new $throwable(...$this->toArray());
    }
}
