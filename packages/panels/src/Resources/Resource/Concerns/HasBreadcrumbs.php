<?php

namespace Filament\Resources\Resource\Concerns;

trait HasBreadcrumbs
{
    protected static ?string $breadcrumb = null;

    public static function getBreadcrumb(): string
    {
        return static::$breadcrumb ?? static::getTitleCasePluralModelLabel();
    }
}
