<?php

namespace Mpietrucha\Support\Concerns;

trait Makeable
{
    public static function make(mixed ...$arguments): static
    {
        /** @phpstan-ignore new.static, argument.type */
        return new static(...$arguments);
    }
}
