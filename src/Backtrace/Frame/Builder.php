<?php

namespace Mpietrucha\Support\Backtrace\Frame;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use Mpietrucha\Support\Backtrace\Frame;
use Mpietrucha\Support\Concerns\Makeable;

/**
 * @phpstan-import-type BacktraceFrame from Frame
 *
 * @implements Arrayable<string, mixed>
 */
class Builder implements Arrayable
{
    use Makeable;

    /**
     * @var BacktraceFrame
     */
    protected array $frame;

    public function __construct(?Frame $frame = null)
    {
        $frame = $frame?->toArray() ?? Property::collection()
            ->keyBy
            ->value()
            ->map
            ->none()
            ->all();

        /** @var BacktraceFrame $frame */
        $this->frame = $frame;
    }

    public function file(?string $file): static
    {
        return $this->set(Property::File, $file);
    }

    public function line(?int $line): static
    {
        return $this->set(Property::Line, $line);
    }

    public function type(?string $type): static
    {
        return $this->set(Property::Type, $type);
    }

    /**
     * @param  null|list<mixed>  $args
     */
    public function args(?array $args): static
    {
        return $this->set(Property::Args, $args);
    }

    public function class(?string $class): static
    {
        return $this->set(Property::ClassName, $class);
    }

    public function object(?object $object): static
    {
        return $this->set(Property::Object, $object);
    }

    public function function(string $function): static
    {
        return $this->set(Property::Function, $function);
    }

    /**
     * @return BacktraceFrame
     */
    public function toArray(): array
    {
        return $this->frame;
    }

    public function build(): Frame
    {
        return $this->toArray() |> Frame::make(...);
    }

    protected function set(Property $property, mixed $value): static
    {
        $frame = $this->toArray();

        /** @var BacktraceFrame $frame */
        $frame = Arr::set($frame, $property->value(), $value);

        $this->frame = $frame;

        return $this;
    }
}
