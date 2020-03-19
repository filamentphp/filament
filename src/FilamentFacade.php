<?php

namespace Filament;

use Illuminate\Support\Facades\Facade;

class FilamentFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'filament';
    }
}