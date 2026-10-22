<?php

namespace Filament\Resources\Pages;

use Filament\Support\Attributes\ServerOnly;

class ManageRecords extends ListRecords
{
    #[ServerOnly]
    public function hasResourceBreadcrumbs(): bool
    {
        return false;
    }

    #[ServerOnly]
    public function getBreadcrumb(): ?string
    {
        return static::$breadcrumb;
    }
}
