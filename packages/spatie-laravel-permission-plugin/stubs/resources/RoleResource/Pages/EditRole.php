<?php

namespace App\Filament\Resources\RoleResource\Pages;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Filament\Resources\RoleResource;
use Filament\Resources\Pages\EditRecord;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    public $permissions;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->permissions = collect($data)->filter(function($permission, $key) {
            return !in_array($key,['name','select_all']) && Str::contains($key, '_');
        })->keys();

        return Arr::only($data,'name');
    }

    protected function afterSave(): void
    {
        $this->record->syncPermissions($this->permissions);
    }
}
