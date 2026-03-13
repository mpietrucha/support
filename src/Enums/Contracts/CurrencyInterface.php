<?php

namespace Mpietrucha\Support\Enums\Contracts;

use BackedEnum;

interface CurrencyInterface extends BackedEnum, EnumInterface
{
    public function symbol(): string;
}
