<?php

use Filament\Tables\Components\TableHeader;
use Filament\Tables\Table;
use Filament\Tests\Tables\TestCase;

use function Filament\Tests\livewireTableWithParts;

uses(TestCase::class);

it('renders the heading, description and header actions', function (): void {
    $html = livewireTableWithParts(
        [TableHeader::make()],
        fn (Table $table): Table => $table
            ->heading('Posts heading')
            ->description('Posts description'),
    )->html();

    expect($html)
        ->toContain('class="fi-ta-header fi-ta-header-adaptive-actions-position"')
        ->toContain('class="fi-ta-header-heading"')
        ->toContain('Posts heading')
        ->toContain('Posts description')
        ->toContain('class="fi-ta-actions fi-align-start fi-wrapped"');
});

it('renders nothing without a heading, description or header actions', function (): void {
    $html = livewireTableWithParts(
        [TableHeader::make()],
        fn (Table $table): Table => $table->headerActions([]),
    )->html();

    expect($html)->not->toContain('class="fi-ta-header');
});

it('hides the header actions while reordering', function (): void {
    $component = livewireTableWithParts(
        [TableHeader::make()],
        fn (Table $table): Table => $table
            ->heading('Posts heading')
            ->reorderable('sort'),
    );

    expect($component->html())->toContain('class="fi-ta-actions fi-align-start fi-wrapped"');

    expect($component->call('toggleTableReordering')->html())
        ->toContain('Posts heading')
        ->not->toContain('class="fi-ta-actions fi-align-start fi-wrapped"');
});
