<?php

namespace Filament\Tests\Fixtures\Livewire;

use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;

class PostsTableWithCustomModalFiltersResetAction extends PostsTable
{
    public function table(Table $table): Table
    {
        return parent::table($table)
            ->filtersResetAction(
                static fn (Action $action) => $action
                    ->label('Custom modal reset filters')
                    ->icon(Heroicon::XMark)
                    ->link(),
            )
            ->filtersLayout(FiltersLayout::Modal);
    }
}
