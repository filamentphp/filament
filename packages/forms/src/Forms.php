<?php

namespace Filament;

use Illuminate\Support\Facades\Facade;

class Forms extends Facade
{
    protected static function getFacadeAccessor()
    {
        return FormsManager::class;
    }
}
