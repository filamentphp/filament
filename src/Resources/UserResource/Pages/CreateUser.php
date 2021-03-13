<?php

namespace Filament\Resources\UserResource\Pages;

use Filament\Filament;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;

class CreateUser extends CreateRecord
{
    public function create()
    {
        $this->validateTemporaryUploadedFiles();

        $this->storeTemporaryUploadedFiles();

        $this->validate();

        $this->record['password'] = Hash::make($this->record['password']);
        unset($this->record['passwordConfirmation']);

        $record = static::getModel()::create($this->record);

        $this->redirect($this->getResource()::generateUrl(static::$showRoute, [
            'record' => $record,
        ]));
    }

    public static function getResource()
    {
        return Filament::userResource();
    }
}
