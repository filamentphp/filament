<?php

namespace Filament\Tests\Fixtures\Livewire;

use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Components\TableContent;
use Filament\Tables\Components\TableEmptyState;
use Filament\Tables\Components\TableFilterIndicators;
use Filament\Tables\Components\TableFilters;
use Filament\Tables\Components\TablePagination;
use Filament\Tables\Components\TableSearch;
use Filament\Tables\Components\TableSelectionIndicator;
use Filament\Tables\Components\TableSortingSettings;
use Filament\Tables\Components\TableToolbar;
use Filament\Tables\Table;

class PostsTableWithCustomLayout extends PostsTable
{
    public function table(Table $table): Table
    {
        return parent::table($table)
            ->persistFiltersInSession(false)
            ->contained(false)
            ->layout(fn (Schema $schema): Schema => $schema->components([
                Grid::make(3)
                    ->schema([
                        Group::make([
                            TableToolbar::make([
                                TableSortingSettings::make(),
                                TableSearch::make(),
                            ]),
                            TableSelectionIndicator::make(),
                            TableFilterIndicators::make(),
                            TableContent::make(),
                            TableEmptyState::make(),
                            TablePagination::make(),
                        ])->columnSpan(2),
                        Section::make('Filters')
                            ->schema([
                                TableFilters::make(),
                            ]),
                    ]),
            ]));
    }
}
