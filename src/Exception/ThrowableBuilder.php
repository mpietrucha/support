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

    protected int $line = 0;

    protected ?Throwable $previous = null;

    protected static ?Closure $buildUsing = null;

    public static function buildUsing(Closure $buildUsing): void
    {
        static::$buildUsing = $buildUsing;
    }

    public function setMessage(string $message, null|bool|float|int|string ...$arguments): static
    {
        $this->message = sprintf($message, ...$arguments);

        return $this;
    }

    public function setLine(int $line): static
    {
        $this->line = $line;

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
            $this->line,
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
        $buildUsing = tap(static::$buildUsing, static function (): void {
            static::$buildUsing = null;
        });

        value($buildUsing, $this);

        return new $throwable(...$this->toArray());
    }
}
