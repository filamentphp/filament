<?php

namespace App\Filament\Audit\Resources\AuditStatusHistoryResource\Pages;

use App\Filament\Audit\Resources\AuditStatusHistoryResource;
use Filament\Resources\Pages\ListRecords;

class ListAuditStatusHistories extends ListRecords
{
    protected static string $resource = AuditStatusHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
