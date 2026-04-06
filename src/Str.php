<?php

declare(strict_types=1);

namespace Mpietrucha\Support;

use Mpietrucha\Support\Str\Concerns\InteractsWithString;

abstract class Str extends \Illuminate\Support\Str
{
    use InteractsWithString;
}
