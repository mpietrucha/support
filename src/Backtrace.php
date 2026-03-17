<?php

namespace Mpietrucha\Support;

use Illuminate\Support\Collection;
use Mpietrucha\Support\Backtrace\Frame;
use Throwable;

/**
 * @phpstan-import-type BacktraceFrame from Frame
 *
 * @phpstan-type FrameCollection Collection<int, Frame>
 */
abstract class Backtrace
{
    /**
     * @return FrameCollection
     */
    public static function throwable(Throwable $throwable): Collection
    {
        return $throwable->getTrace() |> static::build(...);
    }

    /**
     * @return FrameCollection
     */
    public static function get(int $options = DEBUG_BACKTRACE_PROVIDE_OBJECT, int $limit = 0): Collection
    {
        $limit > 0 && $limit++;

        return array_slice(debug_backtrace($options, $limit), 1) |> static::build(...);
    }

    /**
     * @param  array<BacktraceFrame>  $backtrace
     * @return FrameCollection
     */
    protected static function build(array $backtrace): Collection
    {
        return Frame::make(...) |> collect($backtrace)->map(...);
    }
}
