<?php

namespace Filament\Resources\Pages;

class ManageRecords extends ListRecords
{
    public function hasResourceBreadcrumbs(): bool
    {
        return false;
    }

    public function getBreadcrumb(): ?string
    {
        return static::$breadcrumb;
    }
}
