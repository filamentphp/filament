<?php

use Filament\Tables\Components\TableGroup;
use Filament\Tables\Components\TableSearch;
use Filament\Tables\Components\TableToolbar;
use Filament\Tests\Tables\TestCase;

use function Filament\Tests\livewireTableWithParts;

uses(TestCase::class);

it('renders a bare div by default', function (): void {
    $html = livewireTableWithParts([
        TableGroup::make([TableSearch::make()])->id('search-group'),
    ])->html();

    expect($html)
        ->toContain('<div id="search-group">')
        ->not->toContain('fi-growable');
});

it('renders the growable class with `grow()`', function (): void {
    $html = livewireTableWithParts([
        TableToolbar::make([
            TableGroup::make([TableSearch::make()])->id('search-group')->grow(),
        ]),
    ])->html();

    expect($html)->toContain('<div id="search-group" class="fi-growable">');
});

it('merges the growable class with extra classes', function (): void {
    $html = livewireTableWithParts([
        TableGroup::make([TableSearch::make()])
            ->id('search-group')
            ->extraAttributes(['class' => 'custom'])
            ->grow(),
    ])->html();

    expect($html)->toContain('<div id="search-group" class="custom fi-growable">');
});
