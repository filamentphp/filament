<?php

namespace Filament\Resources\UserResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CreateUser extends CreateRecord
{
    public static $resource = UserResource::class;

    public static function getQuery()
    {
        return parent::getQuery()->where('id', '!=', Auth::guard('filament')->user()->id);
    }

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
}
