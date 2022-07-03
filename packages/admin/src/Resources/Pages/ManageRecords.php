<?php

namespace Filament\Resources\Pages;

class ManageRecords extends ListRecords
{
    use ListRecords\Concerns\CanCreateRecords;
    use ListRecords\Concerns\CanDeleteRecords;
    use ListRecords\Concerns\CanEditRecords;

    public function getBreadcrumb(): ?string
    {
        return static::$breadcrumb;
    }
}
