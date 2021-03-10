<?php

namespace Filament;

use Composer\InstalledVersions;
use Filament\Events\ServingFilament;
use Filament\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;

class FilamentManager
{
    public $authorizations = [];

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

    public function can($action, $target)
    {
        $user = $this->auth()->user();

        if ($user->isFilamentAdmin()) return true;

        $targetClass = is_object($target) ? get_class($target) : $target;

        if (count($this->authorizations[$targetClass] ?? [])) {
            $mode = $this->authorizations[$targetClass][0]->mode;

            $fallback = [
                'allow' => false,
                'deny' => true,
            ][$mode];

            foreach ($this->authorizations[$targetClass] as $authorization) {
                if ($mode !== $authorization->mode) continue;

                if (! $user->hasFilamentRole($authorization->role)) continue;

                if (in_array($action, $authorization->exceptActions)) continue;

                if (in_array($action, $authorization->onlyActions)) {
                    if ($mode === 'allow') return true;

                    if ($mode === 'deny') {
                        $fallback = false;

                        continue;
                    }
                }

                if (count($authorization->onlyActions)) continue;

                return [
                    'allow' => true,
                    'deny' => false,
                ][$mode];
            }

            return $fallback;
        }

        $policy = Gate::getPolicyFor($targetClass);

        if ($policy === null || ! method_exists($policy, $action)) {
            return true;
        }

        return Gate::check($action, $target);
    }

    public function getAuthorizations()
    {
        return $this->authorizations;
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

    public function registerAuthorizations($target, $authorizations = [])
    {
        $this->authorizations[$target] = array_merge(
            $this->authorizations[$target] ?? [],
            $authorizations,
        );
    }

    public function registerPage($page)
    {
        $this->pages[] = $page;

        $this->registerAuthorizations($page, $page::authorization());
    }

    public function registerResource($resource)
    {
        $this->resources[] = $resource;

        $this->registerAuthorizations($resource::getModel(), $resource::authorization());
    }

    public function registerRole($role)
    {
        $this->roles[] = $role;
    }

    public function registerScript($name, $path)
    {
        $this->scripts[$name] = $script;
    }

    public function registerStyle($name, $path)
    {
        $this->styles[$name] = $style;
    }

    public function registerWidget($widget)
    {
        $this->widgets[] = $widget;
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
