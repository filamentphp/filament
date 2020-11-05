<?php

namespace Filament\Helpers;

class BladeDirectives 
{
    public static function styles(): string
    {
        return '{!! \Filament::styles() !!}';
    }

    public static function scripts(): string
    {
        return '{!! \Filament::scripts() !!}';
    }
}
