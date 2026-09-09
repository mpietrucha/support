<?php

namespace Mpietrucha\Support\Stringable\Concerns;

use Mpietrucha\Support\Str;
use Mpietrucha\Support\Stringable;

/**
 * @phpstan-require-extends Stringable
 */
trait InteractsWithStringable
{
    public function replacePattern(string $pattern, mixed $replacement, ?string $indicator = null): static
    {
        $value = $this->value();

        return Str::replacePattern($pattern, $replacement, $value, $indicator) |> static::make(...);
    }
}
