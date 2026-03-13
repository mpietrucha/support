<?php

namespace Mpietrucha\Support\Str\Concerns;

trait InteractsWithSymbols
{
    public static function eol(): string
    {
        return PHP_EOL;
    }

    public static function none(?string $append = null): string
    {
        return '';
    }

    public static function tab(): string
    {
        return "\t";
    }

    public static function slash(): string
    {
        return '/';
    }

    public static function backslash(): string
    {
        return '\\';
    }

    public static function dot(): string
    {
        return '.';
    }

    public static function comma(): string
    {
        return ',';
    }

    public static function dash(): string
    {
        return '-';
    }
}
