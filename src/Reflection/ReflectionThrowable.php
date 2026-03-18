<?php

namespace Mpietrucha\Support\Reflection;

use Mpietrucha\Support\Exception\Property;
use Mpietrucha\Support\Instance;
use Mpietrucha\Support\Reflection;
use ReflectionProperty;
use Throwable;

class ReflectionThrowable extends Reflection
{
    public function __construct(Throwable $exception)
    {
        Instance::base($exception) |> parent::__construct(...);
    }

    public function getLineProperty(): ReflectionProperty
    {
        return Property::Line->value |> $this->getProperty(...);
    }

    public function getFileProperty(): ReflectionProperty
    {
        return Property::File->value |> $this->getProperty(...);
    }

    public function getTraceProperty(): ReflectionProperty
    {
        return Property::Trace->value |> $this->getProperty(...);
    }

    public function getMessageProperty(): ReflectionProperty
    {
        return Property::Message->value |> $this->getProperty(...);
    }
}
