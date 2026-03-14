<?php

namespace Mpietrucha\Support;

use Mpietrucha\Support\Enums\Concerns\InteractsWithEnum;
use Mpietrucha\Support\Enums\Contracts\EnumInterface;

enum Lol: string implements EnumInterface
{
    use InteractsWithEnum;

    case Xd = 'lol';
}

class Test
{
    public function xd(): string
    {
        return Lol::Xd->value();
    }
}
