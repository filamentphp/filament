<?php

namespace App\Livewire\Panels\Navigation;

use Filament\Navigation\NavigationItem;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class SidebarCollapsibleOnDesktop extends Page
{
    protected string $view = 'livewire.panels.navigation.empty';

    public function mount()
    {
        filament()
            ->getCurrentPanel()
            ->navigationItems([
                NavigationItem::make()
                    ->label('Products')
                    ->sort(2)
                    ->icon(Heroicon::OutlinedDocumentText)
                    ->url(fn (): string => '#'),
            ])
            ->sidebarCollapsibleOnDesktop();
    }
}
