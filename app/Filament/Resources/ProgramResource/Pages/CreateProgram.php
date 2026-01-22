<?php

namespace App\Filament\Resources\ProgramResource\Pages;

use App\Filament\Resources\ProgramResource;
use App\Services\ProgramService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateProgram extends CreateRecord
{
    protected static string $resource = ProgramResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return app(ProgramService::class)->createProgram($data);
    }
}
