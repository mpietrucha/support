<?php

use Mpietrucha\Support\Enums\Contracts\CurrencyInterface;

enum InteractsWithCurrency: string implements CurrencyInterface
{
    use Mpietrucha\Support\Enums\Concerns\InteractsWithCurrency;
}
