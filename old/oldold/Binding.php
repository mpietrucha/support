<?php

namespace Mpietrucha\Support\Instance;

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

    public function transform(string $value): string
    {
        return $value;
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
}
