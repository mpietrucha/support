<?php

namespace Mpietrucha\Support;

use Mpietrucha\Support\Types;

class Json
{
    public static function isDecodable(?string $string): bool
    {
        return str($string)->isJson();
    }

    public static function decode(?string $string): ?object
    {
        if (! self::isDecodable($string)) {
            return null;
        }

        $value = self::forceDecode($string);

        if (! Types::object($value)) {
            return null;
        }

        return $value;
    }

    public static function decodeToArray(?string $string): ?array
    {
        if (! self::isDecodable($string)) {
            return null;
        }

        $value = self::forceDecode($string, true);

        if (! Types::array($value)) {
            return null;
        }

        return $value;
    }

    protected static function forceDecode(?string $string, bool $array = false): mixed
    {
        return json_decode($string, $array);
    }

    public static function isEncodable(mixed $value): bool
    {
        return Types::object($value) || Types::array($value);
    }

    public static function encode(mixed $value): ?string
    {
        if (! self::isEncodable($value)) {
            return null;
        }

        return self::forceEncode($value);
    }

    public static function forceEncode(mixed $value): ?string
    {
        return json_encode($value) ?? null;
    }
}
