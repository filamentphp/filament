<?php

namespace Filament;

use Illuminate\Support\Str;

abstract class Role
{
    public static $label;

    public static function getLabel()
    {
        if (static::$label) return static::$label;

        return (string) Str::of(class_basename(static::class))
            ->kebab()
            ->replace('-', ' ');
    }
}
