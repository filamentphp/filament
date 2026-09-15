<?php

use Filament\Tables\Components\TablePaginationLinks;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Tables\TestCase;

use function Filament\Tests\livewireTableWithParts;

uses(TestCase::class);

it('renders the page links and the previous and next buttons in one group', function (): void {
    Post::factory()->count(15)->create();

    $html = livewireTableWithParts(
        [TablePaginationLinks::make()],
        fn (Table $table): Table => $table->extremePaginationLinks(),
    )->html();

    expect($html)
        ->toContain('class="fi-pagination-links fi-pagination-part"')
        ->toContain('fi-pagination-items')
        ->toContain('fi-pagination-next-btn')
        ->toContain('rel="last"')
        ->not->toContain('fi-pagination-overview');
});

it('renders nothing when there is a single page', function (): void {
    Post::factory()->count(3)->create();

    $html = livewireTableWithParts([TablePaginationLinks::make()])->html();

    expect($html)->not->toContain('fi-pagination-items');
});
