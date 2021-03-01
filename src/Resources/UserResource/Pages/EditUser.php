<?php

namespace Filament\Resources\UserResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Filament\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class EditUser extends EditRecord
{
    public static $resource = UserResource::class;

    public static $title = 'Edit User';

    public static function getQuery()
    {
        return parent::getQuery()->where('id', '!=', Auth::guard('filament')->user()->id);
    }

    public function save()
    {
        $this->validateTemporaryUploadedFiles();

        $this->storeTemporaryUploadedFiles();

        $this->validate();

        if ($this->record->newPassword) {
            $this->record->password = Hash::make($this->record->newPassword);
        }

        unset($this->record->newPassword);
        unset($this->record->newPasswordConfirmation);

        $this->record->save();

        $this->notify(__(static::$savedMessage));
    }
}
