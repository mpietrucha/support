<?php

namespace Mpietrucha\Support;

use Mpietrucha\Support\Concerns\Makeable;
use Mpietrucha\Support\Exception\BadMethodCallException;
use Mpietrucha\Support\Exception\PendingException;
use Spatie\Invade\StaticInvader;
use Throwable;

/**
 * @phpstan-type ForwardTarget object|class-string
 * @phpstan-type ForwardSource class-string
 */
class Forward
{
    use Makeable;

    /**
     * @param  ForwardTarget  $target
     * @param  ForwardSource  $source
     */
    public function __construct(protected object|string $target, protected string $source)
    {
    }

    /**
     * @return ForwardTarget
     */
    public function target(): object|string
    {
        return $this->target;
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
        $target = $this->target();

        if (Instance::namespace($target) === null) {
            /** @var string $target */
            BadMethodCallException::throw('Invalid forwad target `%s`', $target);
        }

        try {
            $invader = invade($target);

            return match (true) {
                $invader instanceof StaticInvader => $invader->method($method)->call(...$arguments),
                default => $invader->$method(...$arguments)
            };
        } catch (Throwable $e) {
            BadMethodCallException::configure(function (PendingException $exception) use ($e) {
                $exception->previous($e);
            });

            BadMethodCallException::throw('Unable to forward call `%s::%s()`', $this->source(), $method);
        }
    }
}
