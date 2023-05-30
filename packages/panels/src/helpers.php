<?php

use Filament\Contracts\Plugin;
use Filament\FilamentManager;

if (! function_exists('filament')) {
    function filament(?string $plugin = null): FilamentManager | Plugin
    {
        /** @var FilamentManager $filament */
        $filament = app('filament');

        if ($plugin !== null) {
            return $filament->getPlugin($plugin);
        }

        return $filament;
    }
}
