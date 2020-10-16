<?php

namespace Filament;

class Features
{
    /**
     * Determine if the given feature is enabled.
     *
     * @param  string  $feature
     * @return bool
     */
    public static function enabled(string $feature)
    {
        return in_array($feature, config('filament.features', []));
    }

    /**
     * Determine if the application manages users.
     *
     * @return bool
     */
    public static function managesUsers()
    {
        return static::enabled(static::users());
    }

    /**
     * Enable the users feature.
     *
     * @return string
     */
    public static function users()
    {
        return 'users';
    }
}