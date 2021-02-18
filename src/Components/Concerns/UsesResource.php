<?php

namespace Filament\Components\Concerns;

use Filament\Resources\Route;

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

    protected function filters()
    {
        return static::getResource()::filters();
    }

    public static function getResource()
    {
        return static::$resource;
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

    public static function registerRoute($uri, $name)
    {
        return new Route(static::class, $uri, $name);
    }
}
