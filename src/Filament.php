<?php

namespace Filament;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\HtmlString;

class Filament
{
    /**
     * Indicates if Filament routes will be registered.
     *
     * @var bool
     */
    public static $registersRoutes = true;

    /**
     * Configure Filament to not register its routes.
     *
     * @return static
     */
    public function ignoreRoutes()
    {
        static::$registersRoutes = false;

        return new static;
    }
    
    /**
     * `base` path
     *
     * @param string $path
     * @return string
     */
    public function basePath($path = '')
    {
        return __DIR__.'/../'.ltrim($path, '/');
    }

    /**
     * `dist` path
     *
     * @param string $path
     * @return string
     */
    public function distPath($path = '')
    {
        return $this->basePath('dist/'.ltrim($path, '/'));
    }

    public function handling(): boolean
    {
        return preg_match('#^'.config('filament.prefix.route').'($|/)'.'#i', Request::path());
    }

    public function styles(): HtmlString
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
        $src = route('filament.assets.css', $cssInfo);

        return new HtmlString('
            <!-- Filament Styles -->
            <link rel="stylesheet" href="'.$src.'">
        ');
    }

    public function scripts(): Htmlstring
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
        $src = route('filament.assets.js', $jsInfo);

        return new HtmlString('
            <!-- Filament Scripts -->
            <script src="'.$src.'" data-turbolinks-eval="false"></script>
        ');
    }

    protected function getAsset($key): string
    {
        $manifest = json_decode(file_get_contents($this->distPath('mix-manifest.json')), true);

        return $manifest[$key];
    }

    /** @return mixed */
    protected function getPublicAsset($key)
    {
        if (!file_exists(public_path('vendor/filament/mix-manifest.json'))) {
            return;
        }

        $manifest = json_decode(file_get_contents(public_path('vendor/filament/mix-manifest.json')), true);

        return $manifest[$key];
    }
}
