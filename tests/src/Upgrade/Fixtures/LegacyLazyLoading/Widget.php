<?php

namespace Filament\Widgets;

abstract class Widget
{
    protected static bool $isLazy = true;

    protected static ?bool $isLazyLoaded = null;

    public static function isLazy(): bool
    {
        return static::isLazyLoaded();
    }

    public static function isLazyLoaded(): bool
    {
        return static::$isLazyLoaded ?? static::$isLazy;
    }
}
