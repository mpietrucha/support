<?php

namespace Mpietrucha\Support\Backtrace;

use Mpietrucha\Support\Enums\Concerns\InteractsWithEnum;
use Mpietrucha\Support\Enums\Contracts\EnumInterface;
use Mpietrucha\Support\Str;

enum FrameProperty: string implements EnumInterface
{
    use InteractsWithEnum;

    case File = 'file';

    case Line = 'line';

    case Type = 'type';

    case Args = 'args';

    case ClassName = 'class';

    case Object = 'object';

    case Function = 'function';

    public function none(): ?string
    {
        return match ($this) {
            self::Function => Str::none(),
            default => null
        };
    }
}
