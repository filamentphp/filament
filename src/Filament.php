<?php

namespace Filament;

use App\Providers\RouteServiceProvider;
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

    /**
     * @return false|int
     */
    public function handling()
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

        return new HtmlString("
            <!-- Filament Scripts -->
            <script src=\"".route('filament.assets.js', $jsInfo)."\" data-turbolinks-eval=\"false\"></script>
        ");
    }

    /** @return mixed */
    protected function getAsset(string $key)
    {
        $manifest = json_decode(file_get_contents($this->distPath('mix-manifest.json')), true);

        if (! isset($manifest[$key])) {
            return;
        }

        return $manifest[$key];
    }

    /** @return mixed */
    protected function getPublicAsset(string $key)
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

    /**
     * @psalm-suppress UndefinedMethod
     */
    public function resources(): Collection
    {
        $resources_path = app_path('Filament/Resources');

        File::ensureDirectoryExists($resources_path);

        return collect(File::files($resources_path))->map(function ($file) {
            $basename = $file->getBasename('.'.$file->getExtension());
            return Container::getInstance()->getNamespace().'Filament\\Resources\\'.$basename;
        })->filter(function ($class) {
            if (! class_exists($class)) {
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
     * @psalm-suppress UndefinedClass
     * 
     * @return string
     */
    public function home()
    {
        return Features::hasDashboard() ? 
            config('filament.home', route('filament.dashboard')) 
            : RouteServiceProvider::HOME;
    }

    /**
     * Format bytes
     *
     * @param  integer $size
     * @param  integer $precision
     * @return string|integer
     */
    public function formatBytes($size, $precision = 0)
    {
        if ($size > 0) {
            $base = log($size) / log(1024);
            $suffixes = ['bytes', 'KB', 'MB', 'GB', 'TB'];
            return round(pow(1024, $base - floor($base)), $precision).$suffixes[intval($base)];
        } else {
            return $size;
        }
    }

    public function storage(): \Illuminate\Filesystem\FilesystemAdapter
    {   
        return Storage::disk(config('filament.storage_disk'));
    }

    /**
     * Generates an asset URL with optional image manipulations.
     * 
     * @psalm-suppress UndefinedInterfaceMethod
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
        $urlBuilder = UrlBuilderFactory::create('', config('app.key'));
        return route('filament.image', ['path' => ltrim($urlBuilder->getUrl($path, $manipulations), '/')]);
    }

    /**
     * Determines if an asset is a valid image based on approved MIME Types.
     * 
     * @param string $file
     *  
     * @return bool
     */
    public function isImage($file)
    {
        return in_array($this->storage()->getMimeType($file), [
            'image/jpeg',
            'image/png',
            'image/gif',
        ]);
    }
}
