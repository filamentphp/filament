<?php

use Filament\Contracts\Plugin;
use Filament\FilamentManager;

if (! function_exists('filament')) {
    /**
     * @return ($plugin is null ? FilamentManager : Plugin)
     */
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
