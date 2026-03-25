<?php

namespace Mpietrucha\Support\Backtrace;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use Mpietrucha\Support\Concerns\Makeable;

/**
 * @phpstan-type BacktraceFrame array{function: string, line?: int, file?: string, class?: class-string, type?: '->'|'::', args?: list<mixed>, object?: object}
 *
 * @implements Arrayable<string, mixed>
 */
class Frame implements Arrayable
{
    use Makeable;

    /**
     * @param  BacktraceFrame  $frame
     */
    public function __construct(protected array $frame)
    {
    }

    public static function build(?Frame $frame = null): FrameBuilder
    {
        return FrameBuilder::make($frame);
    }

    /**
     * @return BacktraceFrame
     */
    public function toArray(): array
    {
        return $this->frame;
    }

    public function getFile(): ?string
    {
        /** @var null|string */
        return FrameProperty::File |> $this->get(...);
    }

    public function getLine(): ?int
    {
        /** @var null|int */
        return FrameProperty::Line |> $this->get(...);
    }

    public function getType(): ?string
    {
        /** @var null|string */
        return FrameProperty::Type |> $this->get(...);
    }

    /**
     * @return null|array<mixed>
     */
    public function getArgs(): ?array
    {
        /** @var null|array<mixed> */
        return FrameProperty::Args |> $this->get(...);
    }

    /**
     * @return null|class-string
     */
    public function getClass(): ?string
    {
        /** @var null|class-string */
        return FrameProperty::ClassName |> $this->get(...);
    }

    public function getObject(): ?object
    {
        /** @var null|object */
        return FrameProperty::Object |> $this->get(...);
    }

    public function getFunction(): string
    {
        /** @var string */
        return FrameProperty::Function |> $this->get(...);
    }

    protected function get(FrameProperty $frameProperty): mixed
    {
        $frameProperty = $frameProperty->value;

        $frame = $this->toArray();

        return Arr::get($frame, $frameProperty);
    }
}
