<?php

use Filament\Tables\Components\TableFilters;
use Filament\Tables\Components\TableFiltersTrigger;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;
use Filament\Tests\Tables\TestCase;

use function Filament\Tests\livewireTableWithParts;

uses(TestCase::class);

function renderTableFiltersTrigger(FiltersLayout $filtersLayout): string
{
    return livewireTableWithParts(
        [TableFiltersTrigger::make()],
        fn (Table $table): Table => $table->filtersLayout($filtersLayout),
    )
        ->filterTable('is_published')
        ->html();
}

it('renders the dropdown with the active filters badge for `FiltersLayout::Dropdown`', function (): void {
    expect(renderTableFiltersTrigger(FiltersLayout::Dropdown))
        ->toContain('fi-ta-filters-dropdown')
        ->toContain('fi-badge')
        ->not->toContain('fi-ta-filters-modal');
});

it('renders the modal for `FiltersLayout::Modal`', function (): void {
    expect(renderTableFiltersTrigger(FiltersLayout::Modal))
        ->toContain('fi-ta-filters-modal')
        ->not->toContain('fi-ta-filters-dropdown');
});

it('renders the sidebar trigger for `FiltersLayout::BeforeContent`', function (): void {
    expect(renderTableFiltersTrigger(FiltersLayout::BeforeContent))
        ->toContain('x-ref="filtersTriggerActionContainer"')
        ->toContain('x-on:click="toggleFiltersDropdown"')
        ->not->toContain('fi-ta-filters-dropdown');
});

it('renders nothing for `FiltersLayout::AboveContent`', function (): void {
    expect(renderTableFiltersTrigger(FiltersLayout::AboveContent))
        ->not->toContain('filtersTriggerActionContainer')
        ->not->toContain('fi-ta-filters-dropdown')
        ->not->toContain('fi-ta-filters-modal');
});

it('renders nothing when the layout places a `TableFilters` part', function (): void {
    $html = livewireTableWithParts(
        [TableFilters::make(), TableFiltersTrigger::make()],
        fn (Table $table): Table => $table->filtersLayout(FiltersLayout::Dropdown),
    )->html();

    expect($html)
        ->toContain('class="fi-ta-filters"')
        ->not->toContain('fi-ta-filters-dropdown')
        ->not->toContain('fi-ta-filters-modal');
});
