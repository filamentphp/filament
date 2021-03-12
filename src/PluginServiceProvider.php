<?php

namespace Filament;

use Illuminate\Support\ServiceProvider;
use Illuminate\Filesystem\Filesystem;
use Livewire\LiveWire;
use ReflectionClass;
use Str;
use Symfony\Component\Finder\SplFileInfo;

abstract class PluginServiceProvider extends ServiceProvider
{

    protected $pages = [];

    protected $resources = [];

    protected $roles = [];

    protected $scripts = [];

    protected $styles = [];

    protected $widgets = [];

    public function register()
    {
        $this->app->booting(function () {
            foreach ($this->pages() as $page) {
                Filament::registerPage($page);
                $this->registerWithLivewire($resource);
            }

            foreach ($this->resources() as $resource) {
                Filament::registerResource($resource);
                $this->registerWithLivewire($resource);
            }

            foreach ($this->roles() as $role) {
                Filament::registerRole($role);
            }

            foreach ($this->widgets() as $widget) {
                Filament::registerWidget($widget);
            }

            Filament::serving(function () {
                foreach ($this->scripts() as $name => $path) {
                    Filament::registerScript($name, $path);
                }

                foreach ($this->styles() as $name => $path) {
                    Filament::registerScript($name, $path);
                }

                Filament::provideToScript($this->scriptData());
            });
        });
    }

    protected function pages()
    {
        return $this->pages;
    }

    protected function resources()
    {
        return $this->resources;
    }

    protected function roles()
    {
        return $this->roles;
    }

    protected function scripts()
    {
        return $this->scripts;
    }

    protected function scriptData()
    {
        return [];
    }

    protected function styles()
    {
        return $this->styles;
    }

    protected function widgets()
    {
        return $this->widgets;
    }

    private function registerWithLivewire($resource)
    {
        $filesystem = new Filesystem();
        $reflector = new \ReflectionClass($resource);
        $directory = Str::of($reflector->getFileName())->before('.php');
        $namespace = Str::of($resource)->before('.php');

        collect($filesystem->allfiles($directory))
        ->map(function (SplFileInfo $file) use ($namespace) {
            return (string) Str::of($namespace)
                ->append('\\', $file->getRelativePathname())
                ->replace(['/', '.php'], ['\\', '']);
        })
        ->each(function ($class) use ($namespace) {
            $alias = Str::of($class)
                ->replace(['/', '\\'], '.')
                ->explode('.')
                ->map([Str::class, 'kebab'])
                ->implode('.');
                Livewire::component($alias, $class);
        });
    }
}
