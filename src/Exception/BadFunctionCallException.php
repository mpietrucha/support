<?php

namespace Mpietrucha\Support\Exception;

use Mpietrucha\Support\Exception\Concerns\InteractsWithThrowable;

class BadFunctionCallException extends \BadFunctionCallException
{
    use InteractsWithThrowable;
}
