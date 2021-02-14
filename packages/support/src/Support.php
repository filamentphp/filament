<?php

namespace Filament;

use Illuminate\Support\Facades\Facade;

class Support extends Facade
{
    protected static function getFacadeAccessor()
    {
        return SupportManager::class;
    }
}
