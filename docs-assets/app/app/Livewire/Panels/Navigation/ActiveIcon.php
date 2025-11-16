<?php

namespace App\Livewire\Panels\Navigation;

use Filament\Navigation\NavigationItem;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class ActiveIcon extends Page
{
    protected string $view = 'livewire.panels.navigation.empty';

    public function mount()
    {
        filament()
            ->getCurrentPanel()
            ->navigationItems([
                NavigationItem::make()
                    ->label('Settings Inactive')
                    ->activeIcon(Heroicon::OutlinedDocumentText)
                    ->icon(Heroicon::OutlinedCog)
                    ->url(fn (): string => '#'),
                NavigationItem::make('')
                    ->label('Settings Active')
                    ->activeIcon(Heroicon::Cog)
                    ->icon(Heroicon::Cog)
                    ->isActiveWhen(fn () => request()->path() === 'panels/navigation/active-icon')
                    ->url(fn (): string => '#'),
            ]);
    }
}
