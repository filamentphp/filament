<?php

namespace Filament\Resources\UserResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Filament\Resources\UserResource;
use Illuminate\Support\Facades\Auth;

class ListUsers extends ListRecords
{
    public static $resource = UserResource::class;

    public static $title = 'Users';

    public static function getQuery()
    {
        return parent::getQuery()->where('id', '!=', Auth::guard('filament')->user()->id);
    }
}
