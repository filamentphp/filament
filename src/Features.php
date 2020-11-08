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
     * Determine if the application allows user registration.
     *
     * @return bool
     */
    public static function registersUsers()
    {
        return static::enabled(static::registration());
    }

    /**
     * Determine if the application has resources.
     *
     * @return bool
     */
    public static function hasResources()
    {
        return static::enabled(static::resources());
    }

    /**
     * Enable the user registration feature.
     *
     * @return string
     */
    public static function registration()
    {
        return 'registration';
    }

    /**
     * Enable the resources feature.
     *
     * @return string
     */
    public static function resources()
    {
        return 'resourceModels';
    }
}