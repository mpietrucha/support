<?php

namespace Mpietrucha\Support\Concerns;

trait HasFactory
{
    public static function create(): self
    {
        return new self(...func_get_args());
    }
}
