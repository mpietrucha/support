<?php

namespace Mpietrucha\Support\Tokenizer;

use Mpietrucha\Support\Concerns\Makeable;
use PhpToken;

class Token extends PhpToken
{
    use Makeable;

    public function id(): int
    {
        return $this->id;
    }

    public function text(): string
    {
        return $this->text;
    }

    public function line(): int
    {
        return $this->line;
    }

    public function position(): int
    {
        return $this->pos;
    }

    public function name(): ?string
    {
        return $this->getTokenName();
    }

    public function ignored(): bool
    {
        return $this->isIgnorable();
    }
}
