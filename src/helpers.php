<?php

namespace Filament;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use League\Glide\Urls\UrlBuilderFactory;

if (! function_exists('Filament\format_attributes')) {
    function format_attributes($attributes = [])
    {
        return collect($attributes)
            ->map(fn ($value, $key) => "{$key}={$value}")
            ->join(' ');
    }
}

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

if (! function_exists('Filament\get_asset_id')) {
    function get_asset_id($path)
    {
        $manifestPath = __DIR__ . '/../dist/mix-manifest.json';

        if (! file_exists($manifestPath)) return null;

        $manifest = json_decode(file_get_contents($manifestPath), true);

        if (! array_key_exists($path, $manifest)) return null;

        $path = $manifest[$path];

        if (! str_contains($path, 'id=')) return null;

        $id = (string) Str::of($path)->after('id=');

        return $id;
    }
}

if (! function_exists('Filament\get_image_url')) {
    function get_image_url($path, $manipulations = [])
    {
        $urlBuilder = UrlBuilderFactory::create('', config('app.key'));

        return route('filament.image', ['path' => ltrim($urlBuilder->getUrl($path, $manipulations), '/')]);
    }
}

if (! function_exists('Filament\get_media_contents')) {
    function get_media_contents($path)
    {
        $disk = Storage::disk(config('filament.default_filesystem_disk'));
        
        if (! $disk->exists($path)) return;

        return $disk->get($path);
    }
}
