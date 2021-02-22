<?php

namespace Filament\Resources\Pages;

use Filament\Resources\Route;

class Page extends \Filament\Pages\Page
{
    protected static $resource;

    public static function getResource()
    {
        return static::$resource;
    }

    public static function routeTo($uri, $name)
    {
        return new Route(static::class, $uri, $name);
    }

    protected static function getModel()
    {
        return static::getResource()::getModel();
    }

    protected static function getQuery()
    {
        return static::getModel()::query();
    }
}
