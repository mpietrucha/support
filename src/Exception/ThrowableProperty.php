<?php

namespace Mpietrucha\Support\Exception;

use Mpietrucha\Support\Enums\Concerns\InteractsWithEnum;
use Mpietrucha\Support\Enums\Contracts\EnumInterface;

enum ThrowableProperty: string implements EnumInterface
{
    use InteractsWithEnum;

    case File = 'file';

    case Line = 'line';

    case Trace = 'trace';

    case Message = 'message';
}
