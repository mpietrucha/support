<?php

namespace Mpietrucha\Support\Exception;

use Closure;
use Illuminate\Contracts\Support\Arrayable;
use Mpietrucha\Support\Concerns\Makeable;
use Throwable;

/**
 * @implements Arrayable<int, mixed>
 *
 * @phpstan-type PendingArray array{int, null|Throwable}
 */
class PendingException implements Arrayable
{
    use Makeable;

    protected int $line = 0;

    protected ?Throwable $previous = null;

    /**
     * @return PendingArray
     */
    public static function run(Closure $configurator): array
    {
        $pending = static::make();

        $configurator($pending);

        return $pending->toArray();
    }

    public function line(int $line): static
    {
        $this->line = $line;

        return $this;
    }

    public function previous(Throwable $previous): static
    {
        $this->previous = $previous;

        return $this;
    }

    /**
     * @return PendingArray
     */
    public function toArray(): array
    {
        return [
            $this->line,
            $this->previous,
        ];
    }
}
