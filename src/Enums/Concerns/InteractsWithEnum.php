<?php

namespace Mpietrucha\Support\Enums\Concerns;

use Illuminate\Support\Collection;
use Mpietrucha\Support\Enums\Contracts\EnumInterface;

/**
 * @phpstan-require-implements EnumInterface
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
