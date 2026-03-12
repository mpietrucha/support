<?php

namespace Mpietrucha\Support;

use Mpietrucha\Support\Concerns\Makeable;
use Mpietrucha\Support\Exception\BadMethodCallException;
use Mpietrucha\Support\Exception\PendingException;
use Spatie\Invade\StaticInvader;
use Throwable;

/**
 * @phpstan-type ForwardDestination object|class-string
 * @phpstan-type ForwardSource class-string
 */
class Forward
{
    use Makeable;

    /**
     * @param  ForwardDestination  $destination
     * @param  ForwardSource  $source
     */
    public function __construct(protected object|string $destination, protected string $source)
    {
    }

    /**
     * @return ForwardDestination
     */
    public function destination(): object|string
    {
        return $this->destination;
    }

    /**
     * @return ForwardSource
     */
    public function source(): string
    {
        return $this->source;
    }

    public function get(string $method, mixed ...$arguments): mixed
    {
        return $this->eval($method, $arguments);
    }

    /**
     * @param  iterable<mixed>  $arguments
     */
    public function eval(string $method, iterable $arguments): mixed
    {
        $destination = $this->destination();

        $namespace = Instance::namespace($destination);

        try {
            $invader = invade($destination);

            return match (true) {
                $invader instanceof StaticInvader => $invader->method($method)->call(...$arguments),
                default => $invader->$method(...$arguments)
            };
        } catch (Throwable $e) {
            BadMethodCallException::configure(function (PendingException $exception) use ($e) {
                $exception->previous($e);
            });

            BadMethodCallException::throw('Failed to forward method %s::%s()', $namespace, $method);
        }
    }
}
