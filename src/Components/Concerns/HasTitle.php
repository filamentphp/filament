<?php

namespace Filament\Components\Concerns;

use Illuminate\Support\Str;

trait HasTitle
{
    protected static function getTitle()
    {
        if (property_exists(static::class, 'title')) return static::$title;

        return (string) Str::of(class_basename(static::class))
            ->kebab()
            ->replace('-', ' ')
            ->title();
    }
}
