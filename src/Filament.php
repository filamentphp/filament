<?php

namespace Filament;

use Illuminate\Support\Facades\Facade;

class Filament extends Facade
{
    protected static function getFacadeAccessor()
    {
        return FilamentManager::class;
    }
}
