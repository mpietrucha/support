<?php

namespace Mpietrucha\Support\Exception;

use Mpietrucha\Support\Exception\Concerns\InteractsWithThrowable;

class RangeException extends \RangeException
{
    use InteractsWithThrowable;
}
