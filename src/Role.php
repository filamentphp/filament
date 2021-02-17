<?php

namespace Filament;

use Filament\Resources\Authorization;
use Illuminate\Support\Str;

class Role
{
    public static $label;

    public static function allow()
    {
        return new Authorization(static::class, 'allow');
    }

    public static function deny()
    {
        return new Authorization(static::class, 'deny');
    }

    public static function getLabel()
    {
        if (static::$label) return static::$label;

        return (string) Str::of(class_basename(static::class))
            ->beforeLast('Role')
            ->kebab()
            ->replace('-', ' ');
    }
}
