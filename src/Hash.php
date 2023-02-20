<?php

namespace Mpietrucha\Support;

use Exception;

class Hash
{
    public static function __callStatic(string $algorithm, array $arguments): string
    {
        $arguments = collect($arguments);

        if ($arguments->count() === 0) {
            throw new Exception('Hash needs at least one argument');
        }

        return hash($algorithm, $arguments->toWord());
    }
}
