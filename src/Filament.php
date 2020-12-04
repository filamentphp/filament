<?php

namespace Filament;

use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\{
    Request,
    File,
    Storage,
    Route,
};
use Illuminate\Support\{
    Str,
    Collection,
    HtmlString,
};
use League\Glide\Urls\UrlBuilderFactory;
use Filament\Resource;

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

    public function handling(): bool
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

        return new HtmlString('
            <!-- Filament Styles -->
            <link rel="stylesheet" href="'.route('filament.assets.css', $cssInfo).'">
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

        return new HtmlString('
            <!-- Filament Scripts -->
            <script src="'.route('filament.assets.js', $jsInfo).'" data-turbolinks-eval="false"></script>
        ');
    }

    /** @return mixed */
    protected function getAsset($key)
    {
        $manifest = json_decode(file_get_contents($this->distPath('mix-manifest.json')), true);

        if (!isset($manifest[$key])) {
            return;
        }

        return $manifest[$key];
    }

    /** @return mixed */
    protected function getPublicAsset($key)
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

    public function resources(): Collection
    {
        $resources_path = app_path('Filament/Resources');

        File::ensureDirectoryExists($resources_path);

        return collect(File::files($resources_path))->map(function ($file) {
            $basename = $file->getBasename('.'.$file->getExtension());
            return Container::getInstance()->getNamespace().'Filament\\Resources\\'.$basename;
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

    /**
     * Returns the path to the "home" route for Filament.
     * 
     * @return string
     */
    public function home()
    {
        $home = config('filament.home');
        return Route::has($home) ? route($home) : $home;
    }

    /**
     * Generates a secure Glide image URL.
     * 
     * @link https://glide.thephpleague.com/1.0/config/security/
     * 
     * @param string $path
     * @param array  $manipulations
     *  
     * @return mixed
     */
    public function image($path, $manipulations = [])
    {
        if (empty($manipulations)) {
            return Storage::disk(config('filament.disk'))->url($path);
        }

        $urlBuilder = UrlBuilderFactory::create(null, config('app.key'));
        return route('filament.image', ['path' => ltrim($urlBuilder->getUrl($path, $manipulations), '/')]);
    }
}
