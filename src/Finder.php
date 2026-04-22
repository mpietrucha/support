<?php

namespace Mpietrucha\Support;

use Illuminate\Support\LazyCollection;
use Mpietrucha\Support\Concerns\Makeable;
use Symfony\Component\Finder\Finder as SymfonyFinder;
use Symfony\Component\Finder\SplFileInfo;

class Finder extends SymfonyFinder
{
    use Makeable;

    /**
     * @return LazyCollection<string, SplFileInfo>
     */
    public function get(): LazyCollection
    {
        return LazyCollection::make($this);
    }
}
