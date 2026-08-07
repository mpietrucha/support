<?php

namespace Mpietrucha\Support\Number\Concerns;

use Illuminate\Support\Arr;

trait InteractsWithNumber
{
    public static function numericGreaterThanZero(mixed ...$arguments): bool
    {
        return Arr::every(
            $arguments,
            static fn (mixed $argument): bool => is_numeric($argument) && $argument >= 0
        );
    }
}
