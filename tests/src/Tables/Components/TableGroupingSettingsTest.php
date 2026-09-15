<?php

use Filament\Tables\Components\TableGroupingSettings;
use Filament\Tables\Table;
use Filament\Tests\Tables\TestCase;

use function Filament\Tests\livewireTableWithParts;

uses(TestCase::class);

it('renders the grouping select for a table with groups', function (): void {
    $html = livewireTableWithParts([TableGroupingSettings::make()])->html();

    expect($html)
        ->toContain('class="fi-ta-grouping-settings"')
        ->toContain('x-model="group"')
        ->toContain('x-model="direction"');
});

it('renders nothing for a table without groups', function (): void {
    $html = livewireTableWithParts(
        [TableGroupingSettings::make()],
        fn (Table $table): Table => $table->groups([]),
    )->html();

    expect($html)->not->toContain('fi-ta-grouping-settings');
});
