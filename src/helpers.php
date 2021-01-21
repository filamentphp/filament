<?php

namespace Filament;

use League\Glide\Urls\UrlBuilderFactory;

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

if (! function_exists('Filament\get_image_url')) {
    function get_image_url($path, $manipulations = [])
    {
        $urlBuilder = UrlBuilderFactory::create('', config('app.key'));

        return route('filament.image', ['path' => ltrim($urlBuilder->getUrl($path, $manipulations), '/')]);
    }
}

if (! function_exists('Filament\is_image')) {
    function is_image($file)
    {
        return in_array(Filament::storage()->getMimeType($file), [
            'image/jpeg',
            'image/gif',
            'image/png',
        ], true);
    }
}
