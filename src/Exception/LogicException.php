<?php

namespace Mpietrucha\Support\Exception;

use Mpietrucha\Support\Exception\Concerns\InteractsWithThrowable;

class LogicException extends \LogicException
{
    use InteractsWithThrowable;
}
