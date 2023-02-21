<?php

use Mpietrucha\Support\Types;
use Illuminate\Support\Collection;

if (! function_exists('collect_config') && function_exists('config') && function_exists('collect')) {
    function collect_config(string $key, Collection $default = new Collection): mixed {
        $value = config($key);

        if (! $value) {
            return $default;
        }

        if (! Types::array($value)) {
            return $value;
        }

        return collect($value);
    }
}
