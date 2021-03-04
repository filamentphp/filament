<?php

namespace Filament\Resources\UserResource\Pages;

use Filament\Filament;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Hash;

class EditUser extends EditRecord
{
    public static function getResource()
    {
        return Filament::userResource();
    }

    public function save()
    {
        $this->validateTemporaryUploadedFiles();

        $this->storeTemporaryUploadedFiles();

        $this->validate();

        unset($this->record->passwordConfirmation);

        if ($this->record->password) {
            $this->record->password = Hash::make($this->record->password);
        } else {
            unset($this->record->password);
        }

        $this->record->save();

        $this->record->password = null;
        $this->record->passwordConfirmation = null;

        $this->notify(__(static::$savedMessage));
    }
}
