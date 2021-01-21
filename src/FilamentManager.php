<?php

namespace Filament;

use Filament\Http\Livewire\Dashboard;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use ReflectionClass;
use Symfony\Component\Finder\SplFileInfo;

class FilamentManager
{
    public $navigation;

    public $resources;

    public $roles;

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

    public function navigation()
    {
        if ($this->navigation) return $this->navigation;

        $this->navigation = collect();

        $this->navigation->put(Dashboard::class, (object) [
            'active' => 'filament.dashboard',
            'icon' => 'heroicon-o-home',
            'label' => __('filament::dashboard.title'),
            'sort' => -1,
            'url' => route('filament.dashboard'),
        ]);

        $this->resources()
            ->filter(fn ($resource) => $resource::authorizationManager()->can($resource::getActionFromRoute()))
            ->each(function ($resource) {
                $url = route('filament.resources.' . $resource::getSlug() . '.' . $resource::getActionFromRoute());

                $this->navigation->put($resource, (object) [
                    'active' => (string) Str::of(parse_url($url, PHP_URL_PATH))->after('/')->append('*'),
                    'icon' => $resource::$icon,
                    'label' => (string) Str::of($resource::getLabel())->plural()->title(),
                    'sort' => $resource::$sort,
                    'url' => $url,
                ]);
            });

        return $this->navigation;
    }

    public function resources()
    {
        if ($this->resources) return $this->resources;

        return $this->resources = collect((new Filesystem())->allFiles(app_path('Filament/Resources')))
            ->map(function (SplFileInfo $file) {
                return (string) Str::of('App\\Filament\\Resources')
                    ->append('\\', $file->getRelativePathname())
                    ->replace(['/', '.php'], ['\\', '']);
            })
            ->filter(function ($class) {
                return is_subclass_of($class, Resource::class) && ! (new ReflectionClass($class))->isAbstract();
            });
    }

    public function roles()
    {
        if ($this->roles) return $this->roles;

        return $this->roles = collect((new Filesystem())->allFiles(app_path('Filament/Roles')))
            ->map(function (SplFileInfo $file) {
                return (string) Str::of('App\\Filament\\Roles')
                    ->append('\\', $file->getRelativePathname())
                    ->replace(['/', '.php'], ['\\', '']);
            })
            ->filter(function ($class) {
                return is_subclass_of($class, Role::class) && ! (new ReflectionClass($class))->isAbstract();
            });
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

        if ($publishedAsset) {
            $assetWarning = ($publishedAsset !== $asset)
                ? '<script>console.warn("Filament: The published style assets are out of date.\n");</script>' : null;

            return $this->styles = new HtmlString("
                <!-- Filament Published Styles -->
                {$assetWarning}
                <link rel=\"stylesheet\" href=\"/vendor/filament{$publishedAsset}\">
            ");
        }

        parse_str(parse_url($asset, PHP_URL_QUERY), $cssInfo);

        return $this->styles = new HtmlString('
            <!-- Filament Styles -->
            <link rel="stylesheet" href="' . route('filament.assets.css', $cssInfo) . '">
        ');
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
