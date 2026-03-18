<?php

namespace Mpietrucha\Support\Exception;

use Closure;
use Illuminate\Contracts\Support\Arrayable;
use Mpietrucha\Support\Concerns\Makeable;
use Mpietrucha\Support\Str;
use Throwable;

/**
 * @implements Arrayable<int, mixed>
 *
 * @phpstan-type ThrowableArray array{string, int, null|Throwable}
 */
class Builder implements Arrayable
{
    use Makeable;

    protected ?string $message = null;

    protected int $line = 0;

    protected ?Throwable $previous = null;

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

    public function setPrevious(Throwable $previous): static
    {
        $this->previous = $previous;

        return $this;
    }

    /**
     * @return ThrowableArray
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
     * @return ThrowableArray
     */
    public function build(?Closure $tap = null, ?string $message = null, null|bool|float|int|string ...$arguments): array
    {
        if ($tap) {
            $tap($this);
        }

        if ($message) {
            $this->setMessage($message, ...$arguments);
        }

        return $this->toArray();
    }
}
