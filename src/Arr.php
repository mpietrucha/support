<?php

declare(strict_types=1);

namespace Mpietrucha\Support;

use Illuminate\Support\Arr as IlluminateArr;
use Mpietrucha\Support\Arr\Concerns\InteractsWithArray;

abstract class Arr extends IlluminateArr
{
    use InteractsWithArray;
}
