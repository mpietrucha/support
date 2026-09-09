<?php

declare(strict_types=1);

namespace Mpietrucha\Support;

use Illuminate\Support\Stringable as IlluminateStringable;
use Mpietrucha\Support\Concerns\Makeable;
use Mpietrucha\Support\Stringable\Concerns\InteractsWithStringable;

class Stringable extends IlluminateStringable
{
    use InteractsWithStringable;
    use Makeable;
}
