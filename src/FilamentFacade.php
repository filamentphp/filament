<?php

namespace Filament;

use Illuminate\Support\Facades\Facade;

class FilamentFacade extends Facade
{
    /**
     * {@inheritDoc}
     */
    protected static function getFacadeAccessor()
    {
        return Filament::class;
    }
}
