<?php

declare(strict_types=1);

namespace Mpietrucha\Support\Arr\Concerns;

use ArrayAccess;
use InvalidArgumentException;
use Mpietrucha\Support\Arr;

trait InteractsWithArray
{
    /**
     * @param  ArrayAccess<array-key, mixed>|array<mixed>  $array
     * @param  null|array<mixed>  $default
     * @return null|array<mixed>
     */
    public static function tryArray(array|ArrayAccess $array, null|int|string $key, ?array $default = null): ?array
    {
        try {
            return Arr::array($array, $key, $default);
        } catch (InvalidArgumentException) {
            return null;
        }
    }

    /**
     * @param  ArrayAccess<array-key, mixed>|array<mixed>  $array
     */
    public static function tryBoolean(array|ArrayAccess $array, null|int|string $key, ?bool $default = null): ?bool
    {
        try {
            return Arr::boolean($array, $key, $default);
        } catch (InvalidArgumentException) {
            return null;
        }
    }

    /**
     * @param  ArrayAccess<array-key, mixed>|array<mixed>  $array
     */
    public static function tryFloat(array|ArrayAccess $array, null|int|string $key, ?float $default = null): ?float
    {
        try {
            return Arr::float($array, $key, $default);
        } catch (InvalidArgumentException) {
            return null;
        }
    }

    /**
     * @param  ArrayAccess<array-key, mixed>|array<mixed>  $array
     */
    public static function tryInteger(array|ArrayAccess $array, null|int|string $key, ?int $default = null): ?int
    {
        try {
            return Arr::integer($array, $key, $default);
        } catch (InvalidArgumentException) {
            return null;
        }
    }

    /**
     * @param  ArrayAccess<array-key, mixed>|array<mixed>  $array
     */
    public static function tryString(array|ArrayAccess $array, null|int|string $key, ?string $default = null): ?string
    {
        try {
            return Arr::string($array, $key, $default);
        } catch (InvalidArgumentException) {
            return null;
        }
    }
}
