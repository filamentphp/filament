<?php

namespace Filament\Pages;

abstract class CardPage extends BasePage
{
    protected static string $layout = 'filament::components.layouts.card';

    public function getCardWidth(): string
    {
        return 'md';
    }

    public function hasLogo(): bool
    {
        return true;
    }
}
