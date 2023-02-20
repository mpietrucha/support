<?php

namespace Mpietrucha\Support;

use Exception;
use Symfony\Component\Finder\Finder;
use Mpietrucha\Support\Concerns\HasFactory;
use illuminate\Support\Collection;
use Symfony\Component\Finder\SplFileInfo;

class VendorResolver
{
    use HasFactory;

    protected Finder $finder;

    protected ?Collection $builder = null;
    protected ?string $composerJsonPath = null;

    public function __construct()
    {
        $this->finder = new Finder;
    }

    public function __toString(): string
    {
        return $this->name() . DIRECTORY_SEPARATOR . $this->package();
    }

    public function path(): string
    {
        $this->exclude();

        return $this->composerJsonPath . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . (string) $this;
    }

    public function name(): string
    {
        return $this->exclude()->first();
    }

    public function package(): string
    {
        return $this->exclude()->last();
    }

    protected function exclude(): Collection
    {
        if ($this->builder) {
            return $this->builder;
        }

        $composerJsonFile = $this->find();

        if (! $composerJsonFile) {
            throw new Exception('Cannot find composer.json file.');
        }

        $composerJson = json_decode(file_get_contents($composerJsonFile->getPathName()));

        if (! $composerJson->name ?? null) {
            throw new Exception('Given composer.json does not contains name property');
        }

        $this->composerJsonPath = $composerJsonFile->getPath();

        $this->builder = str($composerJson->name)->explode(DIRECTORY_SEPARATOR);

        return $this->exclude();
    }

    protected function find(string $in = __DIR__): ?SplFileInfo
    {
        if ($in === DIRECTORY_SEPARATOR) {
            return null;
        }

        $results = $this->finder->in($in)->name('composer.json');

        foreach ($results as $file) {
            return $file;
        }

        return $this->find(str($in)->beforeLast(DIRECTORY_SEPARATOR));
    }
}
