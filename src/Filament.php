<?php

namespace Filament;

class Filament 
{
    /**
     * Indicates if Filament routes will be registered.
     *
     * @var bool
     */
    public static $registersRoutes = true;

    /**
     * Configure Filament to not register its routes.
     *
     * @return static
     */
    public static function ignoreRoutes()
    {
        static::$registersRoutes = false;

        return new static;
    }

    /**
     * Determine if Filament is supporting user management features.
     *
     * @return bool
     */
    public static function hasUserManagementFeatures()
    {
        return Features::hasUserManagementFeatures();
    }
}
