<?php

namespace Filament\View\Concerns;

trait ChecksNavigationGroup
{
    public static function hasNavigationGroup(): bool
    {
        if (static::$navigationGroup === null) {
            return false;
        }
        return true;
    }
}
