<?php

namespace Filament\Tests\Fixtures\Livewire;

use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Enums\FiltersResetActionPosition;
use Filament\Tables\Table;

class PostsTableWithCustomFooterFiltersResetAction extends PostsTable
{
    public function table(Table $table): Table
    {
        return parent::table($table)
            ->filtersResetAction(
                static fn (Action $action) => $action
                    ->label('Custom footer reset filters')
                    ->icon(Heroicon::XMark)
                    ->link(),
            )
            ->filtersResetActionPosition(FiltersResetActionPosition::Footer);
    }
}
