<?php

namespace Filament\Actions\Concerns;

use Filament\ResourceRoute;

trait UsesResource
{
    protected static $resource;

    protected function columns()
    {
        return static::getResource()::columns();
    }

    protected function fields()
    {
        return static::getResource()::fields();
    }

    protected static function getModel()
    {
        $resource = static::getResource();

        return $resource::$model;
    }

    protected static function getQuery()
    {
        return static::getModel()::query();
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
