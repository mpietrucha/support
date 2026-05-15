<?php

namespace Mpietrucha\Support\Instance;

use Laravel\SerializableClosure\Support\ClosureStream;
use Mpietrucha\Support\Concerns\Compatible;
use Mpietrucha\Support\Concerns\Makeable;
use Mpietrucha\Support\Reflection;
use Mpietrucha\Support\Reflection\ReflectionClosure;
use Mpietrucha\Support\Str;
use ReflectionClass;

/**
 * @phpstan-type BindingSource null|object|class-string
 */
readonly class Binding
{
    use Compatible;
    use Makeable;

    /**
     * @var null|ReflectionClass<object>
     */
    public ?ReflectionClass $source;

    /**
     * @param  BindingSource  $source
     */
    public function __construct(public ReflectionClosure $closure, null|object|string $source = null)
    {
        if ($source !== null) {
            $this->source = Reflection::make($source);

            return;
        }

        $this->source = $closure->getClosureScopeClass();
    }

    public static function compatible(mixed $value, ReflectionClosure $closure): bool
    {
        if (! is_string($value)) {
            return false;
        }

        return Str::contains($value, $closure->getCode());
    }

    /**
     * @param  BindingSource  $source
     */
    public static function for(mixed $value, ReflectionClosure $closure, null|object|string $source = null): ?static
    {
        $incompatible = self::incompatible($value, $closure);

        if ($incompatible) {
            return null;
        }

        return static::make($closure, $source);
    }

    final public function getPrototype(): string
    {
        return 'closure:';
    }

    public function getLine(): false|int
    {
        if ($this->source === null) {
            return $this->closure->getStartLine();
        }

        $method = $this->closure->getName();

        return $this->source->getMethod($method)->getStartLine();
    }

    public function getFile(): false|string
    {
        return $this->source?->getFileName() ?? $this->closure->getFileName();
    }

    public function transformLine(int $line): int
    {
        if ($this->source === null) {
            return $line;
        }

        return $line + $this->getLine() - 2;
    }

    public function transformFile(string $file): string
    {
        return $this->getFile() ?: $file;
    }

    public function transformMessage(string $message): string
    {
        $isMessage = Str::is(
            sprintf($definition = '%s on line %s', '*}', '*'),
            $message
        );

        if (! $isMessage) {
            return $this->transformFunction($message);
        }

        $line = (int) Str::match(
            sprintf($definition, '/', '(\d+)/'),
            $message
        );

        $value = sprintf(
            $definition,
            $this->getFile(),
            $this->transformLine($line),
        );

        $code = Str::after($message, $this->getPrototype());

        return Str::replace($code, $value, $message) |> $this->transformPrototype(...);
    }

    public function transformFunction(string $function): string
    {
        $value = sprintf(
            '%s:%s',
            $this->getFile(),
            $this->getLine(),
        );

        $code = Str::between($function, $this->getPrototype(), '}');

        return Str::replace($code, $value, $function) |> $this->transformPrototype(...);
    }

    public function transformPrototype(string $value): string
    {
        $prototype = sprintf('%s:', ClosureStream::STREAM_PROTO);

        return Str::replace($prototype, $this->getPrototype(), $value);
    }
}
