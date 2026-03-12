<?php

namespace Mpietrucha\Support\Exception;

use Mpietrucha\Support\Exception\Concerns\InteractsWithThrowable;

class OverflowException extends \OverflowException
{
    use InteractsWithThrowable;
}
