<?php

namespace Filament\Pages;

abstract class SimplePage extends BasePage
{
    protected static string $layout = 'filament-panels::components.layout.simple';

    public function hasLogo(): bool
    {
        return true;
    }
}
