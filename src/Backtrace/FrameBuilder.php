<?php

namespace Mpietrucha\Support\Backtrace;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use Mpietrucha\Support\Concerns\Makeable;

/**
 * @phpstan-import-type BacktraceFrame from Frame
 *
 * @implements Arrayable<string, mixed>
 */
class FrameBuilder implements Arrayable
{
    use Makeable;

    /**
     * @var BacktraceFrame
     */
    protected array $frame;

    public function __construct(?Frame $frame = null)
    {
        $frame = $frame?->toArray() ?? FrameProperty::collection()
            ->keyBy
            ->value
            ->map
            ->unknown()
            ->all();

        /** @var BacktraceFrame $frame */
        $this->frame = $frame;
    }

    public function setFile(?string $file): static
    {
        return $this->set(FrameProperty::File, $file);
    }

    public function setLine(?int $line): static
    {
        return $this->set(FrameProperty::Line, $line);
    }

    public function setType(?string $type): static
    {
        return $this->set(FrameProperty::Type, $type);
    }

    /**
     * @param  null|list<mixed>  $args
     */
    public function setArgs(?array $args): static
    {
        return $this->set(FrameProperty::Args, $args);
    }

    public function setClass(?string $class): static
    {
        return $this->set(FrameProperty::ClassName, $class);
    }

    public function setObject(?object $object): static
    {
        return $this->set(FrameProperty::Object, $object);
    }

    public function setFunction(string $function): static
    {
        return $this->set(FrameProperty::Function, $function);
    }

    /**
     * @return BacktraceFrame
     */
    public function toArray(): array
    {
        /** @var BacktraceFrame */
        return $this->frame |> Arr::whereNotNull(...);
    }

    public function build(): Frame
    {
        return $this->toArray() |> Frame::make(...);
    }

    protected function set(FrameProperty $frameProperty, mixed $value): static
    {
        if ($value === null) {
            return $this;
        }

        $frame = $this->toArray();

        /** @phpstan-ignore assign.propertyType */
        $this->frame = Arr::set($frame, $frameProperty->value, $value);

        return $this;
    }
}
