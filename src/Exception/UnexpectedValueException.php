<?php

namespace Mpietrucha\Support\Exception;

use Mpietrucha\Support\Exception\Concerns\InteractsWithThrowable;

class UnexpectedValueException extends \UnexpectedValueException
{
    use InteractsWithThrowable;
}
