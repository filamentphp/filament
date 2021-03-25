<?php

namespace Filament\Resources\UserResource\Pages;

use Filament\Filament;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;

class CreateUser extends CreateRecord
{
    public static function getResource()
    {
        return Filament::userResource();
    }

    protected function beforeCreate()
    {
        $this->record['password'] = Hash::make($this->record['password']);
        unset($this->record['passwordConfirmation']);
    }
}
