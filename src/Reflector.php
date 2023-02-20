<?php

namespace Mpietrucha\Support;

use ReflectionClass;
use Illuminate\Support\Collection;
use Mpietrucha\Factory\HasFactory;
use Closure;

class Reflector extends ReflectionClass
{
    use HasFactory;

    public function traits(?Closure $filter = null): Collection
    {
        $traits = collect(class_uses($this->name))->map(fn (string $trait) => self::create($trait));

        return $this->filtered($traits, $filter);
    }

    public function methods(?Closure $filter = null): Collection
	{
		$methods = collect($this->getMethods());

		return $this->filtered($methods, $filter);
	}

    public function getSnakeName(): string
    {
        return str($this->getShortName())->snake();
    }

    protected function filtered(Collection $results, ?Closure $filter): Collection
    {
        return $results->when(! is_null($filter), fn (Collection $results) => $results->filter($filter));
    }
}
