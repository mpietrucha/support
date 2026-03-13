<?php

namespace Mpietrucha\Support\Filesystem;

use Mpietrucha\Support\Filesystem;
use Symfony\Component\Filesystem\Path as Adapter;

abstract class Path
{
    public static function delimiter(): string
    {
        return '/';
    }

    public static function join(string ...$paths): string
    {
        return Adapter::join(...$paths);
    }

    public static function name(string $path): string
    {
        return static::normalize($path) |> Filesystem::basename(...);
    }

    public static function finish(string $path, string $name): string
    {
        if (static::name($path) === $name) {
            return $path;
        }

        return static::join($path, $name);
    }

    public static function canonicalize(string $path): string
    {
        return Adapter::canonicalize($path);
    }

    public static function normalize(string $path): string
    {
        return Adapter::normalize($path);
    }

    public static function directory(string $path, ?int $level = null): string
    {
        if ($level === 0) {
            return $path;
        }

        $directory = Adapter::getDirectory($path);

        if ($level === null) {
            return $directory;
        }

        if ($level <= 1) {
            return $directory;
        }

        return static::directory($directory, --$level);
    }

    public static function home(): string
    {
        return Adapter::getHomeDirectory();
    }

    public static function root(string $path): string
    {
        return Adapter::getRoot($path);
    }

    public static function nameWithoutExtension(string $path, ?string $extension = null): string
    {
        return Adapter::getFilenameWithoutExtension($path, $extension);
    }

    public static function absolute(string $path, string $directory): string
    {
        return Adapter::makeAbsolute($path, $directory);
    }

    public static function relative(string $path, string $directory): string
    {
        return Adapter::makeRelative($path, $directory);
    }

    public static function get(string $path): string
    {
        $canonicalized = static::canonicalize($path);

        return realpath($canonicalized) ?: $canonicalized;
    }

    public static function build(string $path, ?string $directory = null): string
    {
        if ($directory === null) {
            return static::get($path);
        }

        return static::absolute($path, $directory);
    }

    public static function cwd(string $path): string
    {
        $cwd = Cwd::get();

        return static::build($path, $cwd);
    }
}
