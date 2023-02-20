<?php

namespace Mpietrucha\Support\Concerns;

use Mpietrucha\Support\VendorResolver;

trait HasVendor
{
    public function vendor(): VendorResolver
    {
        return VendorResolver::create();
    }
}
