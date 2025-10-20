<?php

namespace App\Livewire\Panels\Navigation;

use Filament\Navigation\NavigationItem;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class Group extends Page
{
    protected string $view = 'livewire.panels.navigation.empty';

    public function mount()
    {
        filament()
            ->getCurrentPanel()
            ->navigationItems([
                NavigationItem::make()
                    ->label('Bank Accounts')
                    ->url(fn (): string => '#')
                    ->group('Settings')
                    ->icon(Heroicon::OutlinedCurrencyDollar),
            ]);
    }
}
