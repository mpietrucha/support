<?php

namespace Mpietrucha\Support\Filesystem;

use Mpietrucha\Support\Filesystem;
use Mpietrucha\Support\Str;
use Symfony\Component\Filesystem\Path as SymfonyPath;

abstract class Path
{
    public static function delimiter(): string
    {
        return Str::slash();
    }

    public static function join(string ...$paths): string
    {
        return SymfonyPath::join(...$paths);
    }

    public static function name(string $path): string
    {
        return static::normalize($path) |> Filesystem::basename(...);
    }

    public static function canonicalize(string $path): string
    {
        return SymfonyPath::canonicalize($path);
    }

    public static function normalize(string $path): string
    {
        return SymfonyPath::normalize($path);
    }

    public static function directory(string $path, ?int $level = null): string
    {
        if ($level === 0) {
            return $path;
        }

        $directory = SymfonyPath::getDirectory($path);

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
        return SymfonyPath::getHomeDirectory();
    }

    public static function root(string $path): string
    {
        return SymfonyPath::getRoot($path);
    }

    public static function nameWithoutExtension(string $path, ?string $extension = null): string
    {
        return SymfonyPath::getFilenameWithoutExtension($path, $extension);
    }

    public static function absolute(string $path, string $directory): string
    {
        return SymfonyPath::makeAbsolute($path, $directory);
    }

    public static function relative(string $path, string $directory): string
    {
        return SymfonyPath::makeRelative($path, $directory);
    }

    public static function get(string $path): string
    {
        $path = static::canonicalize($path);

        return realpath($path) ?: $path;
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
        return static::build($path, Cwd::get());
    }
}
