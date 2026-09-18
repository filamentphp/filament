<?php

namespace Filament\Tests\Fixtures\Livewire;

use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Enums\FiltersResetActionPosition;
use Filament\Tables\Table;

class PostsTableWithPositionedFiltersResetAction extends PostsTable
{
    public string $resetActionPosition = 'header';

    public function table(Table $table): Table
    {
        return parent::table($table)
            ->filtersLayout(match ($this->resetActionPosition) {
                'modal' => FiltersLayout::Modal,
                default => FiltersLayout::Dropdown,
            })
            ->filtersResetActionPosition(match ($this->resetActionPosition) {
                'footer' => FiltersResetActionPosition::Footer,
                default => FiltersResetActionPosition::Header,
            });
    }
}
