<?php

namespace Filament\ComponentConcerns;

use Filament\ResourceRoute;

trait UsesResource
{
    protected static $resource;

    public static function getModel()
    {
        $resource = static::getResource();

        return $resource::$model;
    }

    public static function getResource()
    {
        return static::$resource;
    }

    public static function route($uri, $name)
    {
        return new ResourceRoute(static::class, $uri, $name);
    }
}
