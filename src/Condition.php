<?php

namespace Mpietrucha\Support;

use Mpietrucha\Support\Concerns\HasFactory;
use Illuminate\Support\Collection;
use Closure;
use Exception;

class Condition
{
    use HasFactory;

    protected bool $multiple = false;

    public function __construct(protected mixed $default = null, protected Collection $resolvers = new Collection)
    {
    }

    public function defaultEmpty(): self
    {
        $this->default = '';

        return $this;
    }

    public function multiple(bool $mode = true): self
    {
        $this->multiple = $mode;

        return $this;
    }

    public function single(): self
    {
        return $this->multiple(false);
    }

    public function add(mixed $value, mixed $condition): self
    {
        $condition = value($condition);

        if (! $condition) {
            return $this;
        }

        $this->resolvers->push(value($value));

        return $this;
    }

    public function addEmpty(mixed $condition): self
    {
        return $this->add('', $condition);
    }

    public function addNull(mixed $condition): self
    {
        return $this->add(null, $condition);
    }

    public function resolve(?Closure $callback = null): mixed
    {
        return with($this->current(), $callback);
    }

    protected function current(): mixed
    {
        if (! $this->resolvers->count()) {
            return value($this->default);
        }

        if ($this->multiple) {
            return $this->resolvers;
        }

        if ($this->resolvers->count() === 1) {
            return $this->resolvers->first();
        }

        throw new Exception('Only one value should pass without multiple mode.');
    }
}
