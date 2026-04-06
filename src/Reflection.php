<?php

declare(strict_types=1);

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
