<?php

namespace Mpietrucha\Support;

use Mpietrucha\Support\Concerns\Makeable;
use ReflectionClass;

/**
 * @extends ReflectionClass<object>
 */
class Reflection extends ReflectionClass
{
    use Makeable;
}
