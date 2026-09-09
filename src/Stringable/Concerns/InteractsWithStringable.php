<?php

namespace Mpietrucha\Support\Stringable\Concerns;

use Mpietrucha\Support\Str;
use Mpietrucha\Support\Stringable;

trait InteractsWithStringable
{
    public function replacePattern(string $pattern, mixed $replacement, ?string $indicator = null): Stringable
    {
        $value = $this->value();

        return Str::replacePattern($pattern, $replacement, $value, $indicator) |> Stringable::make(...);
    }
}
