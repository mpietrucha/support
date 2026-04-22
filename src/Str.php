<?php

declare(strict_types=1);

namespace Mpietrucha\Support;

use Illuminate\Support\Str as IlluminateStr;
use Mpietrucha\Support\Str\Concerns\InteractsWithString;

abstract class Str extends IlluminateStr
{
    use InteractsWithString;
}
