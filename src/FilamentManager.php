<?php

namespace Filament;

use Illuminate\Container\Container;
use Illuminate\Support\{HtmlString, Str};
use Illuminate\Support\Facades\{File, Request, Storage};
use League\Glide\Urls\UrlBuilderFactory;

class FilamentManager
{
    public function formatBytes($size, $precision = 0)
    {
        if ($size > 0) {
            $base = log($size) / log(1024);
            $suffixes = ['bytes', 'KB', 'MB', 'GB', 'TB'];

            return round(pow(1024, $base - floor($base)), $precision) . $suffixes[intval($base)];
        } else {
            return $size;
        }
    }

    public function handling()
    {
        return preg_match('#^' . config('filament.path') . '($|/)' . '#i', Request::path());
    }

    public function image($path, $manipulations = [])
    {
        $urlBuilder = UrlBuilderFactory::create('', config('app.key'));

        return route('filament.image', ['path' => ltrim($urlBuilder->getUrl($path, $manipulations), '/')]);
    }

    public function isImage($file)
    {
        return in_array($this->storage()->getMimeType($file), [
            'image/jpeg',
            'image/gif',
            'image/png',
        ]);
    }

    public function storage()
    {
        return Storage::disk(config('filament.storage_disk'));
    }

    public function resources()
    {
        $resources_path = app_path('Filament/Resources');

        File::ensureDirectoryExists($resources_path);

        return collect(File::files($resources_path))->map(function ($file) {
            $basename = $file->getBasename('.' . $file->getExtension());

            return Container::getInstance()->getNamespace() . 'Filament\\Resources\\' . $basename;
        })->filter(function ($class) {
            if (!class_exists($class)) {
                return false;
            }

            $reflection = new \ReflectionClass($class);

            return !$reflection->isAbstract() && $reflection->isSubclassOf(Resource::class);
        })->mapWithKeys(function ($class) {
            return [Str::kebab(class_basename($class)) => $class];
        });
    }

    public function scripts()
    {
        $key = '/js/filament.js';
        $asset = $this->getAsset($key);
        $publishedAsset = $this->getPublicAsset($key);

        if ($publishedAsset) {
            $assetWarning = ($publishedAsset !== $asset) ?
                '<script>console.warn("Filament: The published javascript assets are out of date.\n");</script>' : null;

            return new HtmlString("
                <!-- Filament Published Scripts -->
                {$assetWarning}
                <script src=\"/vendor/filament{$publishedAsset}\" data-turbolinks-eval=\"false\"></script>
            ");
        }

        parse_str(parse_url($asset, PHP_URL_QUERY), $jsInfo);

        return new HtmlString("
            <!-- Filament Scripts -->
            <script src=\"" . route('filament.assets.js', $jsInfo) . "\" data-turbolinks-eval=\"false\"></script>
        ");
    }

    protected function getAsset(string $key)
    {
        $manifest = json_decode(file_get_contents($this->distPath('mix-manifest.json')), true);

        if (!isset($manifest[$key])) {
            return;
        }

        return $manifest[$key];
    }

    public function distPath($path = '')
    {
        return $this->basePath('dist/' . ltrim($path, '/'));
    }

    public function basePath($path = '')
    {
        return __DIR__ . '/../' . ltrim($path, '/');
    }

    protected function getPublicAsset(string $key)
    {
        $manifestFile = public_path('vendor/filament/mix-manifest.json');

        if (!file_exists($manifestFile)) {
            return;
        }

        $manifest = json_decode(file_get_contents($manifestFile), true);

        if (!isset($manifest[$key])) {
            return;
        }

        return $manifest[$key];
    }

    public function styles()
    {
        $key = '/css/filament.css';
        $asset = $this->getAsset($key);
        $publishedAsset = $this->getPublicAsset($key);

        if ($publishedAsset) {
            $assetWarning = ($publishedAsset !== $asset) ?
                '<script>console.warn("Filament: The published style assets are out of date.\n");</script>' : null;

            return new HtmlString("
                <!-- Filament Published Styles -->
                {$assetWarning}
                <link rel=\"stylesheet\" href=\"/vendor/filament{$publishedAsset}\">
            ");
        }

        parse_str(parse_url($asset, PHP_URL_QUERY), $cssInfo);

        return new HtmlString('
            <!-- Filament Styles -->
            <link rel="stylesheet" href="' . route('filament.assets.css', $cssInfo) . '">
        ');
    }
}
