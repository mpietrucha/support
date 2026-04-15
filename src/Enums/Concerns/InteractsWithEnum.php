<?php

namespace Mpietrucha\Support\Enums\Concerns;

use Illuminate\Support\Collection;
use Mpietrucha\Support\Enum;
use Mpietrucha\Support\Enums\Contracts\EnumInterface;
use Mpietrucha\Support\Exception\RuntimeException;

/**
 * @phpstan-require-implements EnumInterface
 */
trait InteractsWithEnum
{
    public static function build(mixed $value): static
    {
        $enum = static::class;

        if ($value instanceof $enum) {
            return $value;
        }

        if (! is_string($value)) {
            RuntimeException::throw('Enum `%s` cannot be build from value of type `%s`', $enum, get_debug_type($value));
        }

        /** @var static */
        return Enum::backed($enum)::from($value);
    }

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
