<?php

namespace Mpietrucha\Support;

class Test
{
    public static function a(mixed $a, mixed $b): int
    {
        if (! Number::numericGreaterThanZero($a, $b)) {
            return 0;
        }

        return $a - $b;
    }
}
