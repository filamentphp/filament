<?php

namespace Filament;

use Illuminate\Support\Str;

abstract class Role
{
    public static $label;

    public static function allow()
    {
        return new RoleAuthorization(static::class, 'allow');
    }

    public static function deny()
    {
        return new RoleAuthorization(static::class, 'deny');
    }

    public static function getLabel()
    {
        if (static::$label) return static::$label;

        return Str::of(class_basename(static::class))
            ->beforeLast('Role')
            ->kebab()
            ->replace('-', ' ');
    }
}
