<?php

namespace Mpietrucha\Support\Enums\Contracts;

use Illuminate\Support\Collection;
use UnitEnum;

interface EnumInterface extends UnitEnum
{
    /**
     * @return Collection<int, self>
     */
    public static function collection(): Collection;

    public static function default(): static;
}
