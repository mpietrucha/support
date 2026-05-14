<?php

declare(strict_types=1);

namespace Mpietrucha\Support\Reflection;

use Laravel\SerializableClosure\Support\ReflectionClosure as LaravelReflectionClosure;
use Mpietrucha\Support\Concerns\Makeable;

class ReflectionClosure extends LaravelReflectionClosure
{
    use Makeable;

    public function isUnbound(): bool
    {
        return $this->getClosureThis() === null && $this->getClosureScopeClass() === null;
    }
}
