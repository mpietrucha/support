<?php

namespace Mpietrucha\Support\Exception;

use Mpietrucha\Support\Exception\Concerns\InteractsWithThrowable;

class BadMethodCallException extends \BadMethodCallException
{
    use InteractsWithThrowable;
}
