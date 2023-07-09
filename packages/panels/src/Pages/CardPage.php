<?php

namespace Filament\Pages;

abstract class CardPage extends BasePage
{
    public function getCardWidth(): string
    {
        return 'md';
    }

    public function hasLogo(): bool
    {
        return true;
    }
}
