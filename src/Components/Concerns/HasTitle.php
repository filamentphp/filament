<?php

namespace Filament\Components\Concerns;

use Illuminate\Support\Str;

trait HasTitle
{
    protected static $title;

    protected static function getTitle()
    {
        if (static::$title) return static::$title;

        return (string) Str::of(class_basename(static::class))
            ->kebab()
            ->replace('-', ' ')
            ->title();
    }
}
