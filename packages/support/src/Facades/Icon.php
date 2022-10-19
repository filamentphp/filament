<?php

namespace Filament\Support\Facades;

use Filament\Support\Icons\IconManager;
use Illuminate\Support\Facades\Facade;

class Icon extends Facade
{
    protected static function getFacadeAccessor()
    {
        return IconManager::class;
    }
}
