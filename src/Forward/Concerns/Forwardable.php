<?php

namespace Mpietrucha\Support\Forward\Concerns;

use Mpietrucha\Support\Forward;

/**
 * @phpstan-import-type ForwardTarget from \Mpietrucha\Support\Forward
 * @phpstan-import-type ForwardSource from \Mpietrucha\Support\Forward
 */
trait Forwardable
{
    /**
     * @param  ForwardTarget  $target
     * @param  null|ForwardSource  $source
     */
    public static function forward(object|string $target, ?string $source = null): Forward
    {
        return Forward::make($target, $source ?? static::class);
    }
}
