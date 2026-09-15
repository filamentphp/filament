<?php

use Filament\Tables\Components\TableSelectionIndicator;
use Filament\Tables\Components\TableToolbarActions;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Tables\TestCase;

use function Filament\Tests\livewireTableWithParts;

uses(TestCase::class);

it('renders the selection indicator for a selectable table', function (): void {
    Post::factory()->count(2)->create();

    $html = livewireTableWithParts([TableToolbarActions::make(), TableSelectionIndicator::make()])->html();

    expect($html)
        ->toContain('class="fi-ta-selection-indicator"')
        ->toContain('x-show="getSelectedRecordsCount()"')
        ->toContain('x-on:click="selectAllRecords"');
});

it('renders the reorder indicator instead while reordering', function (): void {
    Post::factory()->count(2)->create();

    $html = livewireTableWithParts(
        [TableToolbarActions::make(), TableSelectionIndicator::make()],
        fn (Table $table): Table => $table->reorderable('sort'),
    )
        ->call('toggleTableReordering')
        ->html();

    expect($html)
        ->toContain('class="fi-ta-reorder-indicator"')
        ->not->toContain('fi-ta-selection-indicator');
});

it('renders nothing for a table without selection', function (): void {
    Post::factory()->count(2)->create();

    $html = livewireTableWithParts(
        [TableSelectionIndicator::make()],
        fn (Table $table): Table => $table->selectable(false),
    )->html();

    expect($html)
        ->not->toContain('fi-ta-selection-indicator')
        ->not->toContain('fi-ta-reorder-indicator');
});

it('renders nothing when the layout has no `TableToolbarActions` part to act on the selection', function (): void {
    Post::factory()->count(2)->create();

    $html = livewireTableWithParts([TableSelectionIndicator::make()])->html();

    expect($html)->not->toContain('fi-ta-selection-indicator');
});
