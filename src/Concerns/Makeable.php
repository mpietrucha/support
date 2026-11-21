<?php

namespace Mpietrucha\Support\Concerns;

trait Makeable
{
    public static function make(mixed ...$arguments): static
    {
        $class = get_called_class();

        /** @phpstan-ignore argument.type, new.noConstructor */
        return new $class(...$arguments);
    }
}
