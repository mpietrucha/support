<?php

namespace Mpietrucha\Support\Instance;

use Laravel\SerializableClosure\SerializableClosure;
use Mpietrucha\Support\Concerns\Makeable;

class SerializableInstance extends SerializableClosure
{
    use Makeable;

    public function __construct(object $instance)
    {
        parent::__construct(static fn (): object => $instance);
    }
}
