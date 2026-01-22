<?php

namespace App\Filament\Resources\ProgramResource\Pages;

use App\Filament\Resources\ProgramResource;
use App\Services\ProgramService;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditProgram extends EditRecord
{
    protected static string $resource = ProgramResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        /** @var \App\Models\Program $record */
        return app(ProgramService::class)->updateProgram($record, $data);
    }
}
