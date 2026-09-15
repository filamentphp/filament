<?php

use Filament\Tables\Components\TableToolbarActions;
use Filament\Tables\Table;
use Filament\Tests\Tables\TestCase;

use function Filament\Tests\livewireTableWithParts;

uses(TestCase::class);

it('renders the visible toolbar actions', function (): void {
    $html = livewireTableWithParts([TableToolbarActions::make()])->html();

    expect($html)
        ->toContain("mountAction('halt'")
        ->not->toContain("mountAction('hidden'");
});

it('renders nothing for a table without toolbar actions', function (): void {
    $html = livewireTableWithParts(
        [TableToolbarActions::make()],
        fn (Table $table): Table => $table->toolbarActions([]),
    )->html();

    expect($html)->not->toContain('mountAction(');
});
