<?php

namespace Mpietrucha\Support;

use Composer\Autoload\ClassLoader;
use Illuminate\Filesystem\Filesystem as IlluminateFilesystem;
use Illuminate\Support\Arr;
use Mpietrucha\Support\Exception\RuntimeException;
use Mpietrucha\Support\Filesystem\Path;
use Mpietrucha\Support\Forward\Concerns\Forwardable;
use Symfony\Component\Process\Process;

/**
 * @mixin IlluminateFilesystem
 */
abstract class Filesystem
{
    use Forwardable;

    protected static ?IlluminateFilesystem $adapter = null;

    /**
     * @param  array<mixed>  $arguments
     */
    public static function __callStatic(string $method, array $arguments): mixed
    {
        $adapter = static::adapter();

        return static::forward($adapter)->eval($method, $arguments);
    }

    public static function adapter(): IlluminateFilesystem
    {
        return static::$adapter ??= new IlluminateFilesystem;
    }

    final public static function unexists(string $path): bool
    {
        return static::adapter()->missing($path);
    }

    public static function cwd(): string
    {
        return getcwd() ?: RuntimeException::throw('Unable to determine the current working directory');
    }

    public static function touch(string $path, ?int $modified = null, ?int $accessed = null): bool
    {
        return @touch($path, $modified, $accessed);
    }

    public static function executable(string $path): bool
    {
        return is_executable($path);
    }

    public static function snapshot(string $path, string $algorithm = 'md5'): ?string
    {
        if (static::unexists($path)) {
            return null;
        }

        $process = sprintf('fd --type f --type d --type l --full-path %s | sort | b3sum', $path) |> Process::fromShellCommandline(...);

        $process->run();

        if ($process->isSuccessful() === false) {
            return null;
        }

        return hash($algorithm, $process->getOutput());
    }

    public static function namespace(string $path): ?string
    {
        $path = Path::get($path);

        /** @var null|string */
        return Arr::map(
            ClassLoader::getRegisteredLoaders(),
            static fn (ClassLoader $loader) => array_find_key(
                $loader->getClassMap(),
                fn (string $file) => Path::canonicalize($file) === $path
            )
        ) |> Arr::whereNotNull(...) |> Arr::first(...);
    }
}
