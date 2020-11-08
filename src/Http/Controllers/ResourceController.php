<?php

namespace Filament\Http\Controllers;

use Illuminate\Container\Container;
use Illuminate\Routing\Route;
use Illuminate\View\View;
use Filament;

class ResourceController extends Controller
{
    public function __invoke(Container $container, Route $route, string $resource, string $action = 'index'): View
    {
        $resourceClass = Filament::resources()->get($resource);
        abort_unless($resourceClass, 400, __("`$resource` is not a valid resource."));

        $component = collect(app($resourceClass)->actions())->get($action);
        abort_unless($component, 400, __("`$action` is not a valid resource action in `$resourceClass`."));

        return call_user_func((new $component), $container, $route);
    }
}