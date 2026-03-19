<?php

namespace Mpietrucha\Support;

use BackedEnum;
use Mpietrucha\Support\Enums\Contracts\EnumInterface;
use Mpietrucha\Support\Exception\InvalidArgumentException;
use Mpietrucha\Support\Exception\RuntimeException;
use UnitEnum;

abstract class Enum
{
    /**
     * @template TClass of object = EnumInterface
     *
     * @param  null|class-string<TClass>  $class
     * @return class-string<BackedEnum&TClass>
     */
    public static function backed(mixed $enum, ?string $class = null): string
    {
        $enum = static::unit($enum);

        if (! is_a($enum, BackedEnum::class, true)) {
            InvalidArgumentException::throw('Enum `%s` is not BackedEnum', $enum);
        }

        /** @phpstan-ignore return.type */
        return $enum;
    }

    /**
     * @template TClass of object = EnumInterface
     *
     * @param  null|class-string<TClass>  $class
     * @return class-string<UnitEnum&TClass>
     */
    public static function unit(mixed $enum, ?string $class = null): string
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
