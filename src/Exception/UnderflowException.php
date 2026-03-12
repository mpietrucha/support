<?php

namespace Mpietrucha\Support\Exception;

use Mpietrucha\Support\Exception\Concerns\InteractsWithThrowable;

class UnderflowException extends \UnderflowException
{
    use InteractsWithThrowable;
}
