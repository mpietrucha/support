<?php

namespace Mpietrucha\Support\Instance;

use Laravel\SerializableClosure\SerializableClosure;
use Mpietrucha\Support\Concerns\Makeable;

class Serializable extends SerializableClosure
{
    use Makeable;

    public function __construct(callable|object $data)
    {
        $data = fn () => $data;

        parent::__construct($data);
    }
}
