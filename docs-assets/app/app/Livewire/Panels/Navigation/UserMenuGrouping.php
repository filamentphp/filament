<?php

namespace App\Livewire\Panels\Navigation;

use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class UserMenuGrouping extends Page
{
    protected string $view = 'livewire.panels.navigation.empty';

    public function mount(): void
    {
        filament()
            ->getCurrentOrDefaultPanel()
            ->userMenuItems([
                [
                    Action::make('settings')
                        ->label('Settings')
                        ->url(fn (): string => '#')
                        ->icon(Heroicon::OutlinedCog6Tooth),
                    Action::make('billing')
                        ->label('Billing')
                        ->url(fn (): string => '#')
                        ->icon(Heroicon::OutlinedBanknotes),
                ],
                [
                    Action::make('documentation')
                        ->label('Documentation')
                        ->url(fn (): string => '#')
                        ->icon(Heroicon::OutlinedBookOpen),
                ],
            ]);
    }
}
