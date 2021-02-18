<?php

namespace Filament;

use Filament\Resources\AuthorizationManager;
use Filament\Resources\Router;
use Illuminate\Support\Str;

class Resource
{
    public static $icon = 'heroicon-o-collection';

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
        return new AuthorizationManager(static::class);
    }

    public static function columns()
    {
        return [];
    }

    public static function fields()
    {
        return [];
    }

    public static function filters()
    {
        return [];
    }

    public static function generateUrl($name = null, $parameters = [], $absolute = true)
    {
        if (! $name) $name = static::router()->getIndexRoute()->name;

        return route('filament.resources.' . static::getSlug() . '.' . $name, $parameters, $absolute);
    }

    public static function getIcon()
    {
        return static::$icon;
    }

    public static function getLabel()
    {
        if (static::$label) return static::$label;

        return (string) Str::of(class_basename(static::$model))
            ->kebab()
            ->replace('-', ' ');
    }

    public static function getSlug()
    {
        if (static::$slug) return static::$slug;

        return (string) Str::of(class_basename(static::$model))
            ->plural()
            ->kebab();
    }

    public static function getSort()
    {
        return static::$sort;
    }

    public static function navigationItems()
    {
        $label = (string) Str::of(static::getLabel())->plural()->title();

        return [
            NavigationItem::make($label, static::generateUrl())
                ->activeRule((string) Str::of(parse_url(static::generateUrl(), PHP_URL_PATH))
                    ->after('/')
                    ->append('*'),
                )
                ->icon(static::getIcon())
                ->sort(static::getSort()),
        ];
    }

    public static function router()
    {
        return new Router(static::class);
    }

    public static function routes()
    {
        return [];
    }
}
