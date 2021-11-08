<?php

namespace Filament;

use Illuminate\Support\Str;

if (! function_exists('Filament\get_asset_id')) {
    function get_asset_id($path)
    {
        $manifestPath = __DIR__ . '/../dist/mix-manifest.json';

        if (! file_exists($manifestPath)) {
            return null;
        }

        $manifest = json_decode(file_get_contents($manifestPath), true);

        $path = "/{$path}";

        if (! array_key_exists($path, $manifest)) {
            return null;
        }

        $path = $manifest[$path];

        if (! str_contains($path, 'id=')) {
            return null;
        }

        return (string) Str::of($path)->after('id=');
    }
}
