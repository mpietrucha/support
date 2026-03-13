<?php

namespace Mpietrucha\Support\Utilizer\Concerns\Utilizable;

use Mpietrucha\Support\Utilizer\Concerns\Utilizable;

/**
 * @method static string utilize()
 */
trait Strings
{
    use Utilizable;

    public static function use(?string $utilizable = null): void
    {
        static::utilizable($utilizable);
    }

    protected static function hydrate(): string
    {
        return '';
    }
}
