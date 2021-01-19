<?php

namespace Filament;

if (! function_exists('Filament\format_bytes')) {
    function format_bytes($size, $precision = 0)
    {
        if ($size > 0) {
            $base = log($size) / log(1024);
            $suffixes = ['bytes', 'KB', 'MB', 'GB', 'TB'];

            return round(1024 ** ($base - floor($base)), $precision) . $suffixes[(int) $base];
        }

        return $size;
    }
}
