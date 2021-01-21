<?php

namespace Filament;

use Illuminate\Support\Str;

abstract class Resource
{
    public static $icon = 'heroicon-o-database';

    public static $label;

    public static $model;

    public static $slug;

    public static $sort = 0;

    public static function actions()
    {
        return [];
    }

    public static function authorization()
    {
        return [];
    }

    public static function authorizationManager()
    {
        return new ResourceAuthorizationManager(static::class);
    }

    public static function columns()
    {
        return [];
    }

    public static function fields()
    {
        return [];
    }

    public static function getLabel()
    {
        if (static::$label) return static::$label;

        return Str::of(class_basename(static::$model))
            ->kebab()
            ->replace('-', ' ');
    }

    public static function getSlug()
    {
        if (static::$slug) return static::$slug;

        return Str::of(class_basename(static::$model))
            ->plural()
            ->kebab();
    }

    public static function hasAction($action)
    {
        return in_array($action, static::actions());
    }

    public static function hasRoute($route)
    {
        return in_array($route, array_keys(static::actions()));
    }

    public static function resolveRouteAction($route)
    {
        if (! static::hasRoute($route)) return null;

        return static::actions()[$route];
    }
}
