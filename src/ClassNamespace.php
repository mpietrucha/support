<?php

namespace Mpietrucha\Support;

use Illuminate\Support\Arr;

abstract class ClassNamespace
{
    public static function delimiter(): string
    {
        return Str::backslash();
    }

    public static function join(string ...$elements): string
    {
        $delimiter = static::delimiter();

        $elements = Arr::map($elements, static function (string $element) use ($delimiter): string {
            return Str::trim($element, $delimiter);
        });

        return Arr::join($elements, $delimiter);
    }

    public static function canonicalize(string $namespace): string
    {
        return Str::start($namespace, static::delimiter());
    }

    public static function name(string $namespace): string
    {
        return class_basename($namespace);
    }

    public static function parent(string $namespace, ?int $level = null): string
    {
        if ($level === 0) {
            return $namespace;
        }

        $namespace = Str::beforeLast($namespace, static::delimiter());

        if ($level === null) {
            return $namespace;
        }

        if ($level <= 1) {
            return $namespace;
        }

        return static::parent($namespace, --$level);
    }
}
