<?php

namespace Mpietrucha\Support;

use Illuminate\Support\Stringable;
use Mpietrucha\Support\Concerns\HasFactory;
use Mpietrucha\Support\Condition;
use Mpietrucha\Support\Types;

class Base64
{
    use HasFactory;

    protected ?Stringable $string;

    protected const START_PATTERN = 'data:';
    protected const END_PATTERN = ';base64,';

    public function __construct(mixed $string)
    {
        $this->string = Condition::create($string = str($string))
            ->addNull($string->isEmpty())
            ->resolve();
    }

    public static function decode(?string $value): ?string
    {
        return Condition::create()
            ->add(fn () => base64_decode($value), ! Types::null($value))
            ->resolve();
    }

    public static function encode(?string $value): ?string
    {
        return Condition::create()
            ->add(fn () => base64_encode($value), ! Types::null($value))
            ->resolve();
    }

    public function contentType(): ?Stringable
    {
        $contentType = $this->string?->between(self::START_PATTERN, self::END_PATTERN);

        if ($contentType?->is($this->string)) {
            return null;
        }

        return $contentType;
    }

    public function type(): ?string
    {
        return $this->contentType()?->before(DIRECTORY_SEPARATOR)->lower();
    }

    public function extension(): ?string
    {
        return $this->contentType()?->after(DIRECTORY_SEPARATOR)->lower();
    }

    public function content(): ?string
    {
        if (! $contentType = $this->contentType()) {
            return null;
        }

        return self::decode($this->string->after(self::END_PATTERN));
    }
}
