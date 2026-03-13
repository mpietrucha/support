<?php

namespace Mpietrucha\Support\Tokenizer;

use Illuminate\Support\Collection;
use Mpietrucha\Support\Concerns\Makeable;
use Mpietrucha\Support\Instance\Path as Adapter;
use Mpietrucha\Support\Tokenizer;
use Mpietrucha\Support\Tokenizer\Path\Name;

/**
 * @phpstan-import-type TokenCollection from Tokenizer
 */
class Path
{
    use Makeable;

    public function __construct(protected Tokenizer $tokenizer)
    {
    }

    public static function for(string $code): static
    {
        return Tokenizer::make($code) |> static::make(...);
    }

    public function namespace(): ?Token
    {
        return Name::get() |> $this->tokens()->first->is(...);
    }

    public function name(): ?Token
    {
        /** @var null|Token */
        return $this->tokens()->pipeThrough([
            fn (Collection $tokens) => Name::previous() |> $tokens->skipUntil->is(...),
            fn (Collection $tokens) => Name::next() |> $tokens->first->is(...),
        ]);
    }

    public function canonicalize(): ?Token
    {
        return Name::canonicalized() |> $this->build(...);
    }

    public function get(bool $canonicalized = false): ?Token
    {
        if ($canonicalized) {
            return $this->canonicalize();
        }

        return Name::get() |> $this->build(...);
    }

    protected function build(int $id): ?Token
    {
        [$namespace, $name] = [$this->namespace(), $this->name()];

        if ($name === null) {
            return null;
        }

        if ($namespace === null) {
            return null;
        }

        if ($id === Name::canonicalized()) {
            $namespace = Adapter::canonicalize($namespace);
        }

        return Token::make($id, Adapter::join($namespace, $name));
    }

    /**
     * @return TokenCollection
     */
    protected function tokens(): Collection
    {
        return $this->tokenizer()->get();
    }

    protected function tokenizer(): Tokenizer
    {
        return $this->tokenizer;
    }
}
