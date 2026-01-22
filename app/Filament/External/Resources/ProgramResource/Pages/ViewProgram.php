<?php

namespace App\Filament\External\Resources\ProgramResource\Pages;

use App\Filament\External\Resources\ProgramResource;
use App\Services\AuditService;
use Filament\Resources\Pages\ViewRecord;

class ViewProgram extends ViewRecord
{
    protected static string $resource = ProgramResource::class;

    public function mount(int | string $record): void
    {
        parent::mount($record);

        // Audit Log for accessing detail
        app(AuditService::class)->log(
            'program.viewed_detail',
            'Program',
            $this->record->id,
            ['actor_type' => 'EXTERNAL']
        );
    }
}
