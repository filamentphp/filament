<?php

namespace Filament;

use Illuminate\Support\ServiceProvider;

abstract class AddonServiceProvider extends ServiceProvider
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
            }

            foreach ($this->resources() as $resource) {
                Filament::registerResource();
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
}
