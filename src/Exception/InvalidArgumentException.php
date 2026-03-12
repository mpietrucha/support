<?php

namespace Mpietrucha\Support\Exception;

use Mpietrucha\Support\Exception\Concerns\InteractsWithThrowable;

class InvalidArgumentException extends \InvalidArgumentException
{
    use InteractsWithThrowable;
}
