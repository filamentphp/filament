<?php

namespace Filament;

if (! function_exists('Filament\format_bytes')) {
    function format_bytes($size, $precision = 0)
    {
        if ($size > 0) {
            $base = log($size) / log(1024);
            $suffixes = ['bytes', 'KB', 'MB', 'GB', 'TB'];

            return round(pow(1024, $base - floor($base)), $precision) . $suffixes[intval($base)];
        } else {
            return $size;
        }
    }
}
