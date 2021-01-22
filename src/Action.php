<?php

namespace Filament;

use Illuminate\Support\Str;
use Livewire\Component;

abstract class Action extends Component
{
    public static $model;

    public static $resource;

    public static $title;

    public static function getTitle()
    {
        if (static::$title) return static::$title;

        return (string) Str::of(class_basename(static::class))
            ->kebab()
            ->replace('-', ' ')
            ->title();
    }

    public static function route($uri, $name)
    {
        return new ResourceRoute(static::class, $uri, $name);
    }
}
