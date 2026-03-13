<?php

namespace Mpietrucha\Support\Enums\Concerns;

use Symfony\Component\Intl\Currencies;

/**
 * @phpstan-require-implements \BackedEnum
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
