<?php

namespace Filament;

class Features
{
    public static function hasDashboard()
    {
        return static::enabled(static::dashboard());
    }

    public static function enabled(string $feature)
    {
        return in_array($feature, config('filament.features', []));
    }

    public static function dashboard()
    {
        return 'dashboard';
    }

    public static function hasResources()
    {
        return static::enabled(static::resources());
    }

    public static function resources()
    {
        return 'resources';
    }

    public static function hasUserProfile()
    {
        return static::enabled(static::profile());
    }

    public static function profile()
    {
        return 'profile';
    }
}
