<?php

namespace Filament\Support;

class BladeDirectives {
    public static function filamentAssets()
    {
        return '{!! \Filament::assets() !!}';
    }
}