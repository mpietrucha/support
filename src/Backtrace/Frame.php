<?php

namespace Mpietrucha\Support\Backtrace;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use Mpietrucha\Support\Backtrace\Frame\Builder;
use Mpietrucha\Support\Backtrace\Frame\Property;
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

    public static function build(?Frame $frame = null): Builder
    {
        return Builder::make($frame);
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
        return Property::File |> $this->get(...);
    }

    public function getLine(): ?int
    {
        /** @var null|int */
        return Property::Line |> $this->get(...);
    }

    public function getType(): ?string
    {
        /** @var null|string */
        return Property::Type |> $this->get(...);
    }

    /**
     * @return null|array<mixed>
     */
    public function getArgs(): ?array
    {
        /** @var null|array<mixed> */
        return Property::Args |> $this->get(...);
    }

    /**
     * @return null|class-string
     */
    public function getClass(): ?string
    {
        /** @var null|class-string */
        return Property::ClassName |> $this->get(...);
    }

    public function getObject(): ?object
    {
        /** @var null|object */
        return Property::Object |> $this->get(...);
    }

    public function getFunction(): string
    {
        /** @var string */
        return Property::Function |> $this->get(...);
    }

    protected function get(Property $property): mixed
    {
        $property = $property->value;

        $frame = $this->toArray();

        return Arr::get($frame, $property);
    }
}
