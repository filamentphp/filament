<?php

namespace Filament\Resources\Pages;

use Filament\Support\Components\Attributes\Unexposed;

class ManageRecords extends ListRecords
{
    #[Unexposed]
    public function hasResourceBreadcrumbs(): bool
    {
        return false;
    }

    #[Unexposed]
    public function getBreadcrumb(): ?string
    {
        return static::$breadcrumb;
    }
}
