<?php

require_once __DIR__.'/vendor/autoload.php';

use Mpietrucha\Support\VendorResolver;

$a = VendorResolver::create();

dd($a->path());
