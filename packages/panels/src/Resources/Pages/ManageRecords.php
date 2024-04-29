<?php

namespace Filament\Resources\Pages;

class ManageRecords extends ListRecords
{
    public function hasResourceBreadcrumbs(): bool
    {
        return false;
    }
}
