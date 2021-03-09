<?php

namespace Filament;

use Composer\InstalledVersions;
use Filament\Events\ServingFilament;
use Filament\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;

class FilamentManager
{
    public $pages = [];

    public $resources = [];

    public $roles = [];

    public $styles = [];

    public $scripts = [];

    public $scriptData = [];

    public $servingCallbacks = [];

    public $widgets = [];

    public function auth()
    {
        return Auth::guard(config('filament.auth.guard', 'filament'));
    }

    public function getPages()
    {
        return $this->pages;
    }

    public function getResources()
    {
        return $this->resources;
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function getScripts()
    {
        return $this->scripts;
    }

    public function getScriptData()
    {
        return array_merge([
            'filamentVersion' => $this->version(),
            'userId' => Filament::auth()->id(),
        ], $this->scriptData);
    }

    public function getStyles()
    {
        return $this->styles;
    }

    public function getWidgets()
    {
        return $this->widgets;
    }

    public function provideToScript($variables)
    {
        $this->scriptData = array_merge($this->scriptData, $variables);
    }

    public function registerPage($page)
    {
        $this->pages = array_merge($this->pages, [$page]);
    }

    public function registerResource($resource)
    {
        $this->resources = array_merge($this->resources, [$resource]);
    }

    public function registerRole($role)
    {
        $this->roles = array_merge($this->roles, [$role]);
    }

    public function registerScript($name, $script)
    {
        $this->scripts[$name] = $script;
    }

    public function registerStyle($name, $style)
    {
        $this->styles[$name] = $style;
    }

    public function registerWidget($widget)
    {
        $this->widgets = array_merge($this->widgets, [$widget]);
    }

    public function serving($callback)
    {
        Event::listen(ServingFilament::class, $callback);
    }

    public function userResource()
    {
        return config('filament.user_resource', UserResource::class);
    }

    public function version()
    {
        if (! class_exists('Composer\\InstalledVersions')) {
            return null;
        }

        return InstalledVersions::getPrettyVersion('filament/filament');
    }
}
