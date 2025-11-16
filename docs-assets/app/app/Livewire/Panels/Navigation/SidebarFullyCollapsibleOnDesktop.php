<?php

namespace App\Livewire\Panels\Navigation;

use Filament\Navigation\NavigationItem;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class SidebarFullyCollapsibleOnDesktop extends Page
{
    protected string $view = 'livewire.panels.navigation.empty';

    public function mount()
    {
        filament()
            ->getCurrentPanel()
            ->navigationItems([
                NavigationItem::make()
                    ->label('Products')
                    ->icon(Heroicon::OutlinedDocumentText)
                    ->sort(2)
                    ->url(fn (): string => '#'),
            ])
            ->sidebarFullyCollapsibleOnDesktop();
    }
}
