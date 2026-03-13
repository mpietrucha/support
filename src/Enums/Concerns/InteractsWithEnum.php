<?php

namespace Mpietrucha\Support\Enums\Concerns;

use BackedEnum;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Mpietrucha\Support\Enums\Contracts\EnumInterface;

/**
 * @phpstan-require-implements EnumInterface
 */
trait InteractsWithEnum
{
    public static function use(): string
    {
        return static::class;
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

    public function name(): string
    {
        return $this->name;
    }

    public function value(): int|string
    {
        /** @var int|string */
        return match (true) {
            $this instanceof BackedEnum => $this->value,
            default => $this->name()
        };
    }

    public function label(): string
    {
        $value = (string) $this->value();

        return match (true) {
            Str::upper($value) === $value => $value,
            default => Str::headline($value)
        };
    }
}
