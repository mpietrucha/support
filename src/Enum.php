<?php

namespace Mpietrucha\Support;

use Mpietrucha\Support\Concerns\Compatible;
use Mpietrucha\Support\Enums\Contracts\EnumInterface;

abstract class Enum
{
    use Compatible;

    public static function compatible(mixed $enum): bool
    {
        if (! is_string($enum)) {
            return false;
        }

        if (! enum_exists($enum)) {
            return false;
        }

        return is_a($enum, EnumInterface::class, true);
    }
}
