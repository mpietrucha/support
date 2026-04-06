<?php

declare(strict_types=1);

namespace Mpietrucha\Support\Exception;

use Mpietrucha\Support\Exception\Concerns\InteractsWithThrowable;

class DomainException extends \DomainException
{
    use InteractsWithThrowable;
}
