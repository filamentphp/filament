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

        return (string) Str::of(class_basename(static::$model))
            ->kebab()
            ->replace('-', ' ');
    }

    public static function route($name = null, $parameters = [], $absolute = true)
    {
        if (! $name) $name = static::router()->getIndexRoute()->name;

        return route('filament.resources.' . static::getSlug() . '.' . $name, $parameters, $absolute);
    }

    public static function router()
    {
        return new ResourceRouter(static::class);
    }

    public static function getSlug()
    {
        if (static::$slug) return static::$slug;

        return (string) Str::of(class_basename(static::$model))
            ->plural()
            ->kebab();
    }

    public static function routes()
    {
        return [];
    }
}
