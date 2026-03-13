<?php

namespace Mpietrucha\Support;

use Illuminate\Support\Collection;
use Mpietrucha\Support\Concerns\Makeable;
use Mpietrucha\Support\Tokenizer\Path;
use Mpietrucha\Support\Tokenizer\Token;

/**
 * @phpstan-type TokenCollection Collection<int, Token>
 */
class Tokenizer
{
    use Makeable;

    /**
     * @var null|TokenCollection
     */
    protected ?Collection $tokens = null;

    public function __construct(protected string $code)
    {
    }

    public function path(): Path
    {
        return Path::make($this);
    }

    /**
     * @return TokenCollection
     */
    public function get(): Collection
    {
        return $this->tokens ??= $this->code() |> Token::tokenize(...) |> collect(...);
    }

    protected function code(): string
    {
        return $this->code;
    }
}
