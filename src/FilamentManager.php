<?php

namespace Filament;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;

class FilamentManager
{
    public $scripts;

    public $storage;

    public $styles;

    protected function getAsset($key)
    {
        $manifest = json_decode(file_get_contents($this->distPath('mix-manifest.json')), true);

        if (! isset($manifest[$key])) {
            return;
        }

        return $manifest[$key];
    }

    protected function getPublicAsset($key)
    {
        $manifestFile = public_path('vendor/filament/mix-manifest.json');

        if (! file_exists($manifestFile)) {
            return;
        }

        $manifest = json_decode(file_get_contents($manifestFile), true);

        if (! isset($manifest[$key])) {
            return;
        }

        return $manifest[$key];
    }

    public function scripts()
    {
        if ($this->scripts) return $this->scripts;

        $key = '/js/filament.js';
        $asset = $this->getAsset($key);
        $publishedAsset = $this->getPublicAsset($key);

        if ($publishedAsset) {
            $assetWarning = ($publishedAsset !== $asset)
                ? '<script>console.warn("Filament: The published javascript assets are out of date.\n");</script>' : null;

            return $this->scripts = new HtmlString("
                <!-- Filament Published Scripts -->
                {$assetWarning}
                <script src=\"/vendor/filament{$publishedAsset}\" data-turbolinks-eval=\"false\"></script>
            ");
        }

        parse_str(parse_url($asset, PHP_URL_QUERY), $jsInfo);

        return $this->scripts = new HtmlString('
            <!-- Filament Scripts -->
            <script src="' . route('filament.assets.js', $jsInfo) . '" data-turbolinks-eval="false"></script>
        ');
    }

    public function storage()
    {
        if ($this->storage) return $this->storage;

        return $this->storage = Storage::disk(config('filament.storage_disk'));
    }

    public function styles()
    {
        if ($this->styles) return $this->styles;

        $key = '/css/filament.css';
        $asset = $this->getAsset($key);
        $publishedAsset = $this->getPublicAsset($key);

        $externalAssets = '
            <link rel="preconnect" href="https://fonts.gstatic.com">
            <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Commissioner:wght@400;500;600;700&family=JetBrains+Mono:ital@0;1&display=swap">
        ';

        if ($publishedAsset) {
            $assetWarning = ($publishedAsset !== $asset)
                ? '<script>console.warn("Filament: The published style assets are out of date.\n");</script>' : null;

            return $this->styles = new HtmlString("
                <!-- Filament Published Styles -->
                {$assetWarning}
                {$externalAssets}
                <link rel=\"stylesheet\" href=\"/vendor/filament{$publishedAsset}\">
            ");
        }

        parse_str(parse_url($asset, PHP_URL_QUERY), $cssInfo);

        return $this->styles = new HtmlString("
            <!-- Filament Styles -->
            {$externalAssets}
            <link rel=\"stylesheet\" href=\"" . route('filament.assets.css', $cssInfo) . "\">
        ");
    }

    public function basePath($path = '')
    {
        return __DIR__ . '/../' . ltrim($path, '/');
    }

    public function distPath($path = '')
    {
        return $this->basePath('dist/' . ltrim($path, '/'));
    }
}
