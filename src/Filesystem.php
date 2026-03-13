<?php

namespace Mpietrucha\Support;

use Composer\Autoload\ClassLoader;
use Illuminate\Filesystem\Filesystem as Adapter;
use Mpietrucha\Support\Exception\RuntimeException;
use Mpietrucha\Support\Filesystem\Concerns\InteractsWithExistence;
use Mpietrucha\Support\Filesystem\Path;
use Mpietrucha\Support\Forward\Concerns\Forwardable;
use Mpietrucha\Support\Instance\Path as Instance;
use Symfony\Component\Process\Process;

/**
 * @mixin Adapter
 */
abstract class Filesystem
{
    use Forwardable, InteractsWithExistence;

    protected static ?Adapter $adapter = null;

    /**
     * @param  array<mixed>  $arguments
     */
    public static function __callStatic(string $method, array $arguments): mixed
    {
        $adapter = static::adapter();

        return static::forward($adapter)->eval($method, $arguments);
    }

    public static function adapter(): Adapter
    {
        return static::$adapter ??= new Adapter;
    }

    public static function cwd(): string
    {
        return getcwd() ?: RuntimeException::throw('System cwd is unavailable');
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

    public static function tokenize(string $path): Tokenizer
    {
        return static::get($path) |> Tokenizer::make(...);
    }

    public static function namespace(string $path, bool $canonicalized = false): ?string
    {
        $loaders = ClassLoader::getRegisteredLoaders() |> collect(...);

        $path = Path::get($path);

        $namespace = $loaders
            ->map
            ->getClassMap()
            ->collapse()
            ->search(fn (string $loaded) => Path::canonicalize($loaded) === $path);

        if (is_string($namespace)) {
            return $canonicalized ? Instance::canonicalize($namespace) : $namespace;
        }

        return static::tokenize($path)
            ->path()
            ->get($canonicalized);
    }
}
