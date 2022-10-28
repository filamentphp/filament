<?php

use Filament\FilamentManager;

if (! function_exists('filament')) {
    function filament(): FilamentManager
    {
        return app('filament');
    }
}
