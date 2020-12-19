<?php

namespace Filament\Http\Controllers;

use Illuminate\Container\Container;
use Illuminate\Routing\Route;
use Filament\Facades\Filament;

class ResourceController extends Controller
{
    /**
     * @psalm-suppress InvalidFunctionCall
     */
    public function __invoke(Container $container, Route $route, string $resource, string $action = 'index'): \Illuminate\View\View
    {
        $resourceClass = Filament::resources()->get($resource);
        abort_unless($resourceClass, 400, __("`$resource` is not a valid resource."));

        $resourceInstance = app($resourceClass);

        abort_unless($resourceInstance->enabled, 403, __("You are not allowed to access this resource."));

        $component = collect($resourceInstance->actions())->get($action);
        abort_unless($component, 400, __("`$action` is not a valid resource action in `$resourceClass`."));

        return call_user_func((new $component), $container, $route);
    }
}