<?php

namespace Filament\Resources\Pages;

class ManageRecords extends ListRecords
{
    public function getBreadcrumb(): ?string
    {
        return static::$breadcrumb;
    }
}
