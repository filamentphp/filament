<?php

use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Components\TableContent;
use Filament\Tables\Components\TableSortingSettings;
use Filament\Tables\Components\TableToolbar;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Tables\TestCase;

use function Filament\Tests\livewireTableWithParts;

uses(TestCase::class);

it('renders the sort selects for a table with a content grid', function (): void {
    $html = livewireTableWithParts(
        [TableSortingSettings::make()],
        fn (Table $table): Table => $table
            ->contentGrid(['md' => 2])
            ->columns([
                Stack::make([
                    TextColumn::make('title')->sortable(),
                    TextColumn::make('rating'),
                ]),
            ]),
    )->html();

    expect($html)
        ->toContain('class="fi-ta-sorting-settings"')
        ->toContain('x-model="column"')
        ->toContain('value="title"');
});

it('renders nothing for a row table', function (): void {
    $html = livewireTableWithParts([TableSortingSettings::make()])->html();

    expect($html)->not->toContain('fi-ta-sorting-settings');
});

it('is not repeated in the content header when placed in a toolbar', function (): void {
    Post::factory()->count(2)->create();

    $html = livewireTableWithParts(
        [
            TableToolbar::make([TableSortingSettings::make()]),
            TableContent::make(),
        ],
        fn (Table $table): Table => $table
            ->contentGrid(['md' => 2])
            ->columns([
                Stack::make([
                    TextColumn::make('title')->sortable(),
                ]),
            ]),
    )->html();

    expect(substr_count($html, 'class="fi-ta-sorting-settings"'))->toBe(1);
});
