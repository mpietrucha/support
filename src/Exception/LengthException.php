<?php

namespace Mpietrucha\Support\Exception;

use Mpietrucha\Support\Exception\Concerns\InteractsWithThrowable;

class LengthException extends \LengthException
{
    use InteractsWithThrowable;
}
