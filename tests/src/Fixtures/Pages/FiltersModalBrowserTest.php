<?php

namespace Filament\Tests\Fixtures\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Schemas\Components\Livewire;
use Filament\Support\Icons\Heroicon;
use Filament\Tests\Fixtures\Livewire\FiltersFocusTable;

class FiltersModalBrowserTest extends Page
{
    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedFunnel;

    protected static bool $shouldRegisterNavigation = false;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('openTableModal')
                ->label('Open table modal')
                ->schema([Livewire::make(FiltersFocusTable::class)])
                ->modalSubmitAction(false)
                ->extraAttributes(['data-testid' => 'table-modal-trigger'])
                ->extraModalWindowAttributes(['data-testid' => 'table-modal']),
        ];
    }
}
