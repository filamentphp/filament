<?php

namespace Filament\Resources\RoleResource\Pages;

use Filament\Resources\RoleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        dd($data);

        return $data;
    }
}
