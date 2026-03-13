<?php

namespace Mpietrucha\Support\Enums\Contracts;

use Illuminate\Support\Collection;
use UnitEnum;

interface EnumInterface extends UnitEnum
{
    /**
     * @return class-string<static>
     */
    public static function use(): string;

    /**
     * @return Collection<int, static>
     */
    public static function collection(): Collection;

    public static function default(): static;

    public function name(): string;

    public function value(): int|string;

    public function label(): string;
}
