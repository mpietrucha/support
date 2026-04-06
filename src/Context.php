<?php

declare(strict_types=1);

namespace Mpietrucha\Support;

abstract class Context
{
    /**
     * @var list<string>
     */
    protected static array $consoles = [
        'cli',
        'phpdb',
    ];

    public static function console(): bool
    {
        return in_array(PHP_SAPI, static::consoles());
    }

    final public static function web(): bool
    {
        return ! static::console();
    }

    /**
     * @return list<string>
     */
    protected static function consoles(): array
    {
        return static::$consoles;
    }
}
