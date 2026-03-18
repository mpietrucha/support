<?php

namespace Mpietrucha\Support\Enums\Concerns;

use Illuminate\Support\Collection;
use UnitEnum;

/**
 * @phpstan-require-implements UnitEnum
 */
trait InteractsWithEnum
{
    /**
     * @return Collection<int, static>
     */
    public static function collection(): Collection
    {
        /** @phpstan-ignore return.type */
        return static::cases() |> collect(...);
    }

    public static function default(): static
    {
        return static::collection()->firstOrFail();
    }
}
