<?php

use Mpietrucha\Support\Enums\Contracts\InteractsWithCurrencyInterface;

enum InteractsWithCurrency: string implements InteractsWithCurrencyInterface
{
    use Mpietrucha\Support\Enums\Concerns\InteractsWithCurrency;
}
