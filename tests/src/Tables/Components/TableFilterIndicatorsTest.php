<?php

use Filament\Tables\Components\TableFilterIndicators;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;
use Filament\Tables\Table;
use Filament\Tests\Tables\TestCase;

use function Filament\Tests\livewireTableWithParts;

uses(TestCase::class);

it('renders badges and the remove all action for an active filter', function (): void {
    $html = livewireTableWithParts([TableFilterIndicators::make()])
        ->filterTable('is_published')
        ->html();

    expect($html)
        ->toContain('class="fi-ta-filter-indicators"')
        ->toContain('Is published')
        ->toContain('removeTableFilters');
});

it('renders nothing when no filter is active', function (): void {
    $html = livewireTableWithParts([TableFilterIndicators::make()])->html();

    expect($html)->not->toContain('fi-ta-filter-indicators');
});

it('does not render the remove all action when no indicator is removable', function (): void {
    $html = livewireTableWithParts(
        [TableFilterIndicators::make()],
        fn (Table $table): Table => $table->filters([
            Filter::make('is_published')
                ->indicateUsing(fn (): array => [Indicator::make('Published only')->removable(false)]),
        ]),
    )
        ->filterTable('is_published')
        ->html();

    expect($html)
        ->toContain('Published only')
        ->not->toContain('removeTableFilters');
});
