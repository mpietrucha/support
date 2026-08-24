<?php

namespace Mpietrucha\Support\Arr\Concerns;

use ArrayAccess;
use Mpietrucha\Support\Arr;

/**
 * @phpstan-type InputArray array<mixed>|ArrayAccess<array-key, mixed>
 */
trait InteractsWithArray
{
    /**
     * @param  InputArray  $array
     * @param  null|array<mixed>  $default
     * @return null|array<mixed>
     */
    public static function tryArray(array|ArrayAccess $array, null|int|string $key, ?array $default = null): ?array
    {
        $value = Arr::get($array, $key, $default);

        return is_array($value) ? $value : null;
    }

    /**
     * @param  InputArray  $array
     */
    public static function tryBoolean(array|ArrayAccess $array, null|int|string $key, ?bool $default = null): ?bool
    {
        $value = Arr::get($array, $key, $default);

        return is_bool($value) ? $value : null;
    }

    /**
     * @param  InputArray  $array
     */
    public static function tryFloat(array|ArrayAccess $array, null|int|string $key, ?float $default = null): ?float
    {
        $value = Arr::get($array, $key, $default);

        return is_float($value) ? $value : null;
    }

    /**
     * @param  InputArray  $array
     */
    public static function tryInteger(array|ArrayAccess $array, null|int|string $key, ?int $default = null): ?int
    {
        $value = Arr::get($array, $key, $default);

        return is_int($value) ? $value : null;
    }

    /**
     * @param  InputArray  $array
     */
    public static function tryString(array|ArrayAccess $array, null|int|string $key, ?string $default = null): ?string
    {
        $value = Arr::get($array, $key, $default);

        return is_string($value) ? $value : null;
    }
}
