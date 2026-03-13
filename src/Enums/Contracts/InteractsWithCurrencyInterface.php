<?php

namespace Mpietrucha\Support\Enums\Contracts;

use BackedEnum;

interface InteractsWithCurrencyInterface extends BackedEnum, InteractsWithEnumInterface
{
    public function symbol(): string;
}
