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
     * Determine if the application is using any user features.
     *
     * @return bool
     */
    public static function hasUserManagementFeatures()
    {
        return static::enabled(static::managesUsers());
    }

    /**
     * Enable the user management feature.
     *
     * @return string
     */
    public static function managesUsers()
    {
        return 'users';
    }
}