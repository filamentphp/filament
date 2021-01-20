<?php

namespace Filament;

use Filament\Http\Livewire\Dashboard;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use League\Glide\Urls\UrlBuilderFactory;
use ReflectionClass;
use Symfony\Component\Finder\SplFileInfo;

class FilamentManager
{
    public function getNavigation()
    {
        $navigation = [];

        $navigation[Dashboard::class] = (object) [
            'active' => ['filament.dashboard'],
            'icon' => 'heroicon-o-home',
            'label' => __('filament::dashboard.title'),
            'path' => route('filament.dashboard'),
            'sort' => 0,
        ];

        $this->getResources()->each(function ($resourceClass) use (&$navigation) {
            $resource = new $resourceClass;

            if ($resource->getDefaultAction()) {
                $path = route('filament.resource', ['resource' => $resource->getSlug()]);

                $navigation[$resourceClass] = (object) [
                    'active' => [
                        parse_url($path, PHP_URL_PATH),
                        parse_url($path, PHP_URL_PATH) . '/*',
                    ],
                    'icon' => $resource->getIcon(),
                    'label' => (string) Str::of($resource->getLabel())->plural()->title(),
                    'path' => $path,
                    'sort' => $resource->getSort(),
                ];
            }
        });

        return $navigation;
    }

    public function getResources()
    {
        return collect((new Filesystem())->allFiles(app_path('Filament/Resources')))
            ->map(function (SplFileInfo $file) {
                return (string) Str::of('App\\Filament\\Resources')
                    ->append('\\', $file->getRelativePathname())
                    ->replace(['/', '.php'], ['\\', '']);
            })
            ->filter(function ($class) {
                return is_subclass_of($class, Resource::class) && ! (new ReflectionClass($class))->isAbstract();
            })
            ->mapWithKeys(function ($class) {
                $resource = new $class;

                return [$resource->getSlug() => $class];
            });
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
        ], true);
    }

    public function storage()
    {
        return Storage::disk(config('filament.storage_disk'));
    }

    public function scripts()
    {
        $key = '/js/filament.js';
        $asset = $this->getAsset($key);
        $publishedAsset = $this->getPublicAsset($key);

        if ($publishedAsset) {
            $assetWarning = ($publishedAsset !== $asset)
                ? '<script>console.warn("Filament: The published javascript assets are out of date.\n");</script>' : null;

            return new HtmlString("
                <!-- Filament Published Scripts -->
                {$assetWarning}
                <script src=\"/vendor/filament{$publishedAsset}\" data-turbolinks-eval=\"false\"></script>
            ");
        }

        parse_str(parse_url($asset, PHP_URL_QUERY), $jsInfo);

        return new HtmlString('
            <!-- Filament Scripts -->
            <script src="' . route('filament.assets.js', $jsInfo) . '" data-turbolinks-eval="false"></script>
        ');
    }

    protected function getAsset(string $key)
    {
        $manifest = json_decode(file_get_contents($this->distPath('mix-manifest.json')), true);

        if (! isset($manifest[$key])) {
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

        if (! file_exists($manifestFile)) {
            return;
        }

        $manifest = json_decode(file_get_contents($manifestFile), true);

        if (! isset($manifest[$key])) {
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
            $assetWarning = ($publishedAsset !== $asset)
                ? '<script>console.warn("Filament: The published style assets are out of date.\n");</script>' : null;

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
