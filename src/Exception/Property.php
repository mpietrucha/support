<?php

namespace Mpietrucha\Support\Exception;

use Mpietrucha\Support\Enums\Concerns\InteractsWithEnum;

enum Property: string
{
    use InteractsWithEnum;

    case File = 'file';

    case Line = 'line';

    case Trace = 'trace';

    case Message = 'message';
}
