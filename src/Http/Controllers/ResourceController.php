<?php

namespace Filament\Http\Controllers;

use Filament\Filament;
use Illuminate\Container\Container;
use Illuminate\Routing\Route;

class ResourceController extends Controller
{
    public function __invoke(Container $container, Route $route, $resource, $actionKey = null, $parameter = null)
    {
        $resourceClass = Filament::getResources()->get($resource);

        abort_unless($resourceClass, 404);

        $resource = new $resourceClass;

        $actionClass = $resource->getAction($actionKey);

        abort_unless($actionClass, 404);

        $action = new $actionClass;

        abort_unless(! $action->hasRouteParameter xor $parameter !== null, 404);

        return call_user_func($action, $container, $route);
    }
}
