<?php

namespace Alpine;

use Illuminate\Support\Facades\Facade;

class AlpineFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'alpine';
    }
}