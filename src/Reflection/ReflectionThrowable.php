<?php

namespace Mpietrucha\Support\Reflection;

use Mpietrucha\Support\Exception\ThrowableProperty;
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
        return ThrowableProperty::Line->value |> $this->getProperty(...);
    }

    public function getFileProperty(): ReflectionProperty
    {
        return ThrowableProperty::File->value |> $this->getProperty(...);
    }

    public function getTraceProperty(): ReflectionProperty
    {
        return ThrowableProperty::Trace->value |> $this->getProperty(...);
    }

    public function getMessageProperty(): ReflectionProperty
    {
        return ThrowableProperty::Message->value |> $this->getProperty(...);
    }
}
