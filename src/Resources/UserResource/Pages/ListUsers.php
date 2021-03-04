<?php

namespace Filament\Resources\UserResource\Pages;

use Filament\Filament;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    public static function getResource()
    {
        return Filament::userResource();
    }
}
