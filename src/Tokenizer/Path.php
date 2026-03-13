<?php

namespace Mpietrucha\Support\Tokenizer;

use Illuminate\Support\Collection;
use Mpietrucha\Support\Concerns\Makeable;
use Mpietrucha\Support\Instance\Path as Adapter;
use Mpietrucha\Support\Tokenizer;

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
        return $this->tokens()->first->is(T_NAME_QUALIFIED);
    }

    public function name(): ?Token
    {
        $previous = [T_CLASS, T_TRAIT, T_INTERFACE, T_ENUM];

        /** @var null|Token */
        return $this->tokens()->pipeThrough([
            fn (Collection $tokens) => $tokens->skipUntil->is($previous),
            fn (Collection $tokens) => $tokens->first->is(T_STRING),
        ]);
    }

    public function value(bool $canonicalized = false): ?string
    {
        [$namespace, $name] = [$this->namespace(), $this->name()];

        if ($name === null) {
            return null;
        }

        if ($namespace === null) {
            return null;
        }

        if ($canonicalized) {
            $namespace = Adapter::canonicalize($namespace);
        }

        return Adapter::join($namespace, $name);
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
