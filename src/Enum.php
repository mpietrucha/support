<?php

namespace Mpietrucha\Support;

use Mpietrucha\Support\Concerns\Compatible;
use Mpietrucha\Support\Enums\Contracts\EnumInterface;
use Mpietrucha\Support\Exception\InvalidArgumentException;
use Mpietrucha\Support\Exception\RuntimeException;
use UnitEnum;

abstract class Enum
{
    use Compatible;

    /**
     * @param  null|class-string  $class
     */
    public static function compatible(mixed $enum, ?string $class = null): bool
    {
        try {
            static::get($enum, $class);

            return true;
        } catch (InvalidArgumentException|RuntimeException $exception) {
            return false;
        }
    }

    /**
     * @template TClass of object = EnumInterface
     *
     * @param  null|class-string<TClass>  $class
     * @return class-string<UnitEnum&TClass>
     */
    public static function get(mixed $enum, ?string $class = null): string
    {
        if (! is_string($enum)) {
            InvalidArgumentException::throw('Value of type `%s` cannot be used as enum', get_debug_type($enum));
        }

        if (! enum_exists($enum)) {
            RuntimeException::throw('Enum `%s` not found', $enum);
        }

        if ($class === null) {
            $class = EnumInterface::class;
        }

        if (! is_a($enum, $class, true)) {
            RuntimeException::throw('Enum `%s` must implement %s', $enum, $class);
        }

        return $enum;
    }
}
