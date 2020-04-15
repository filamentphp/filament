<?php

namespace Filament\Support;

class BladeDirectives {
    /**
     * Get Filament Assets
     * 
     * @return string
     */
    public static function assets()
    {
        return '{!! \Filament::assets() !!}';
    }
}