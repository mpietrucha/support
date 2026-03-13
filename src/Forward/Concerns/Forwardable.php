<?php

namespace Mpietrucha\Support\Forward\Concerns;

use Mpietrucha\Support\Forward;

/**
 * @phpstan-import-type ForwardDestination from \Mpietrucha\Support\Forward
 * @phpstan-import-type ForwardSource from \Mpietrucha\Support\Forward
 */
trait Forwardable
{
    /**
     * @param  ForwardDestination  $destination
     * @param  null|ForwardSource  $source
     */
    public static function forward(object|string $destination, ?string $source = null): Forward
    {
        return Forward::make($destination, $source ?? static::class);
    }
}
