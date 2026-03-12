<?php

namespace Mpietrucha\Support\Exception;

use Mpietrucha\Support\Exception\Concerns\InteractsWithThrowable;

class RuntimeException extends \RuntimeException
{
    use InteractsWithThrowable;
}
