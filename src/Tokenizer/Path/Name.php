<?php

namespace Mpietrucha\Support\Tokenizer\Path;

abstract class Name
{
    public static function get(): int
    {
        return T_NAME_QUALIFIED;
    }

    public static function canonicalized(): int
    {
        return T_NAME_FULLY_QUALIFIED;
    }

    public static function next(): int
    {
        return T_STRING;
    }

    /**
     * @return list<int>
     */
    public static function previous(): array
    {
        return [T_CLASS, T_TRAIT, T_INTERFACE, T_ENUM];
    }
}
