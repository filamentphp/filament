<?php

use Filament\Tables\Components\TableSearch;
use Filament\Tables\Components\TableStack;
use Filament\Tests\Tables\TestCase;

use function Filament\Tests\livewireTableWithParts;

uses(TestCase::class);

it('renders a bare div with its attributes', function (): void {
    $html = livewireTableWithParts([
        TableStack::make([TableSearch::make()])
            ->id('search-stack')
            ->extraAttributes(['class' => 'custom', 'x-cloak' => true]),
    ])->html();

    expect($html)
        ->toContain('<div id="search-stack" class="custom" x-cloak>')
        ->toContain('class="fi-ta-search-field"');
});
