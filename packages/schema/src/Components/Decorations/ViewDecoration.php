<?php

namespace Filament\Schema\Components\Decorations;

class ViewDecoration extends Decoration
{
    public static function make(): static
    {
        $static = app(static::class);
        $static->configure();

        return $static;
    }
}
