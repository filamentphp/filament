<?php

namespace Filament\SpatieLaravelTagsPlugin\Types;

/**
 * An empty type class to model that all tag types are allowed on a component.
 */
class AllTagTypes
{
    public static function make(): static
    {
        return app(static::class);
    }
}
