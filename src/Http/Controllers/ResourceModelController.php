<?php

namespace Filament\Http\Controllers;

use Illuminate\Container\Container;
use Illuminate\Routing\Route;
use Illuminate\View\View;
use Filament;

class ResourceModelController extends Controller
{
    public function __invoke(Container $container, Route $route, string $model, string $action = 'index'): View
    {
        $model = Filament::getResourceModels()->get($model);
        abort_unless($model, 404);

        $component = collect(app($model)->actions())->get($action);
        abort_unless($component, 400, __("`$action` is not a valid resource action in `$model`"));

        return call_user_func((new $component), $container, $route);
    }
}