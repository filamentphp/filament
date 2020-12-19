<?php

namespace Filament\Facades;

use Illuminate\Support\Facades\Facade;

class Filament extends Facade
{
    /**
     * {@inheritDoc}
     */
    protected static function getFacadeAccessor()
    {
        return \Filament\Filament::class;
    }
}
