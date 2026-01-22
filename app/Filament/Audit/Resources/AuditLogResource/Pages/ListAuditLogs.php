<?php

namespace App\Filament\Audit\Resources\AuditLogResource\Pages;

use App\Filament\Audit\Resources\AuditLogResource;
use Filament\Resources\Pages\ListRecords;

class ListAuditLogs extends ListRecords
{
    protected static string $resource = AuditLogResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
