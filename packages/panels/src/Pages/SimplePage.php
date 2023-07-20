<?php

namespace Filament\Pages;

abstract class SimplePage extends BasePage
{
    protected static string $layout = 'filament::components.layout.simple';

    public function hasLogo(): bool
    {
        return true;
    }
}
