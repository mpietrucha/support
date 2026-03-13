<?php

namespace Mpietrucha\Support\Enums\Concerns;

use Mpietrucha\Support\Enums\Contracts\InteractsWithCurrencyInterface;
use Symfony\Component\Intl\Currencies;

/**
 * @phpstan-require-implements InteractsWithCurrencyInterface
 */
trait InteractsWithCurrency
{
    use InteractsWithEnum;

    public function symbol(): string
    {
        /** @phpstan-ignore argument.type */
        return $this->value() |> Currencies::getSymbol(...);
    }
}
