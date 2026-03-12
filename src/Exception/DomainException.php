<?php

namespace Mpietrucha\Support\Exception;

use Mpietrucha\Support\Exception\Concerns\InteractsWithThrowable;

class DomainException extends \DomainException
{
    use InteractsWithThrowable;
}
