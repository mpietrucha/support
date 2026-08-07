<?php

declare(strict_types=1);

namespace Mpietrucha\Support;

use Illuminate\Support\Number as IlluminateNumber;
use Mpietrucha\Support\Number\Concerns\InteractsWithNumber;

abstract class Number extends IlluminateNumber
{
    use InteractsWithNumber;
}
