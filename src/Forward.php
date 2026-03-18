<?php

namespace Mpietrucha\Support;

use Mpietrucha\Support\Concerns\Makeable;
use Mpietrucha\Support\Exception\BadMethodCallException;
use Throwable;

/**
 * @phpstan-type ForwardTarget object|class-string
 * @phpstan-type ForwardSource class-string
 */
readonly class Forward
{
    use Makeable;

    /**
     * @param  ForwardTarget  $target
     * @param  ForwardSource  $source
     */
    public function __construct(public object|string $target, public string $source)
    {
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
        $target = $this->target;

        $namespace = Instance::namespace($target);

        if ($namespace === null) {
            /** @var string $target */
            BadMethodCallException::throw('Invalid forwad target `%s`', $target);
        }

        try {
            return match (true) {
                is_string($target) => $target::$method(...$arguments),
                is_object($target) => $target->$method(...$arguments),
            };
        } catch (Throwable $exception) {
            $message = $exception->getMessage();

            $patterns = [
                sprintf('*Method %s::%s does not exist*', $namespace, $method),
                sprintf('*Call to undefined method %s::%s*', $namespace, $method),
            ];

            if (Str::is($patterns, $message)) {
                BadMethodCallException::throw('Call to undefined method %s::%s()', $this->source, $method);
            }

            throw $exception;
        }
    }
}
