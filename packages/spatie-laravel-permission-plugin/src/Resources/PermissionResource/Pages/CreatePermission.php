<?php

namespace Filament\Resources\PermissionResource\Pages;

use Filament\Resources\PermissionResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePermission extends CreateRecord
{
    protected static string $resource = PermissionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        dd($data);

        return $data;
    }
}
