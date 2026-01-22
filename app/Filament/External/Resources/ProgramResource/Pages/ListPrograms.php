<?php

namespace App\Filament\External\Resources\ProgramResource\Pages;

use App\Filament\External\Resources\ProgramResource;
use App\Services\AuditService;
use Filament\Resources\Pages\ListRecords;

class ListPrograms extends ListRecords
{
    protected static string $resource = ProgramResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
