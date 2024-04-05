<?php

namespace Filament\SpatieLaravelMediaLibraryPlugin\Collections;

/**
 * An empty type class to model that all media collections are allowed on a component.
 */
class AllMediaCollections
{
    public static function make(): static
    {
        return app(static::class);
    }
}
