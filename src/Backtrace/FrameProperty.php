<?php

declare(strict_types=1);

namespace Mpietrucha\Support\Backtrace;

use Mpietrucha\Support\Enums\Concerns\InteractsWithEnum;
use Mpietrucha\Support\Enums\Contracts\EnumInterface;

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

    public function unknown(): ?string
    {
        return match ($this) {
            self::Function => 'unknown',
            default => null
        };
    }
}
