<?php

namespace Mpietrucha\Support\Concerns;

trait Creatable
{
    public static function create(mixed ...$arguments): static
    {
        /** @phpstan-ignore new.static, argument.type */
        return new static(...$arguments);
    }
}
