<?php

namespace Filament\Support\Facades;

use Filament\Support\Assets\AssetManager;
use Illuminate\Support\Facades\Facade;

class Asset extends Facade
{
    protected static function getFacadeAccessor()
    {
        return AssetManager::class;
    }
}
