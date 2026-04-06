<?php

declare(strict_types=1);

namespace Mpietrucha\Support\Concerns;

use Mpietrucha\Support\Str;

/**
 * @method static string utilize()
 */
trait UtilizableStrings
{
    use Utilizable;

    public static function use(?string $utilizable = null): void
    {
        static::utilizable($utilizable);
    }

    protected static function hydrate(): string
    {
        return Str::none();
    }
}
