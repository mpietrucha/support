<?php

namespace Mpietrucha\Support\Concerns;

use Mpietrucha\Support\VendorResolver;

trait HasVendor
{
    public function vendor(): string
    {
        return VendorResolver::create();
    }
}
