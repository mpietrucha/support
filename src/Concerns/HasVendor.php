<?php

namespace Mpietrucha\Support\Concerns;

use Mpietrucha\Support\Vendor;

trait HasVendor
{
    public function vendor(): Vendor
    {
        return Vendor::create();
    }
}
