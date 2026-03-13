<?php

namespace Mpietrucha\Support;

use Countable;
use Illuminate\Support\LazyCollection;
use Mpietrucha\Support\Concerns\Makeable;
use Symfony\Component\Finder\SplFileInfo;

class Finder extends \Symfony\Component\Finder\Finder implements Countable
{
    use Makeable;

    /**
     * @return LazyCollection<string, SplFileInfo>
     */
    public function get(): LazyCollection
    {
        return LazyCollection::make($this);
    }

    public function count(): int
    {
        return $this->get()->count();
    }
}
