<?php

namespace Filament\Resources\UserResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Filament\Resources\UserResource;
use Illuminate\Support\Facades\Hash;

class EditUser extends EditRecord
{
    public static $resource = UserResource::class;

    public function save()
    {
        $this->validateTemporaryUploadedFiles();

        $this->storeTemporaryUploadedFiles();

        $this->validate();

        if ($this->record->password) {
            $this->record->password = Hash::make($this->record->password);
        }

        unset($this->record->password);
        unset($this->record->passwordConfirmation);

        $this->record->save();

        $this->notify(__(static::$savedMessage));
    }
}
