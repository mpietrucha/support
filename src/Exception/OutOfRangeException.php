<?php

namespace Mpietrucha\Support\Exception;

use Mpietrucha\Support\Exception\Concerns\InteractsWithThrowable;

class OutOfRangeException extends \OutOfRangeException
{
    use InteractsWithThrowable;
}
