<?php

namespace App\Livewire\Panels\Navigation;

use Filament\Navigation\MenuItem;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class UserMenuCustomization extends Page
{
    protected string $view = 'livewire.panels.navigation.empty';

    public function mount()
    {
        filament()
            ->getCurrentOrDefaultPanel()
            ->userMenuItems([
                MenuItem::make()
                    ->label('Settings')
                    ->url(fn (): string => '#')
                    ->icon(Heroicon::OutlinedCog6Tooth),
            ]);
    }
}
