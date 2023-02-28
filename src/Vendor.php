<?php

namespace Mpietrucha\Support;

use Exception;
use Symfony\Component\Finder\Finder;
use Mpietrucha\Support\Concerns\HasFactory;
use illuminate\Support\Collection;
use Symfony\Component\Finder\SplFileInfo;

class Vendor
{
    use HasFactory;

    protected ?Collection $builder = null;
    protected ?string $composerJsonPath = null;

    public function __construct(protected Finder $finder = new Finder)
    {
        $this->exclude();
    }

    public function __toString(): string
    {
        return $this->name() . DIRECTORY_SEPARATOR . $this->package();
    }

    public function path(): string
    {
        return $this->composerJsonPath;
    }

    public function name(): string
    {
        return $this->builder->first();
    }

    public function package(): string
    {
        return $this->builder->last();
    }

    protected function exclude(): void
    {
        $composerJsonFile = $this->find();

        if (! $composerJsonFile) {
            throw new Exception('Cannot find composer.json file.');
        }

        $composerJson = Json::decodeToCollection($composerJsonFile->getContents());

        if (! $vendorName = $composerJson->get('name')) {
            throw new Exception('Given composer.json does not contains name property');
        }

        $this->composerJsonPath = $composerJsonFile->getPath();

        $this->builder = str($vendorName)->explode(DIRECTORY_SEPARATOR);
    }

    protected function find(?string $in = null): ?SplFileInfo
    {
        $in ??= $this->extractCallerPath();

        if (! $in || $in === DIRECTORY_SEPARATOR) {
            return null;
        }

        $results = $this->finder->in($in)->name('composer.json');

        foreach ($results as $file) {
            return $file;
        }

        return $this->find(str($in)->beforeLast(DIRECTORY_SEPARATOR));
    }

    protected function extractCallerPath(): ?string
    {
        return collect(debug_backtrace())
            ->pluck('file')
            ->filter(fn (string $path) => ! str($path)->startsWith(__DIR__))
            ->map(fn (string $file) => dirname($file))
            ->first();
    }
}
