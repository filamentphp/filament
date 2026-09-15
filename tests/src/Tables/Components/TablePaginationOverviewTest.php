<?php

use Filament\Tables\Components\TablePaginationOverview;
use Filament\Tables\Enums\PaginationMode;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Tables\TestCase;

use function Filament\Tests\livewireTableWithParts;

uses(TestCase::class);

it('renders the results overview in its own wrapper when placed outside the pagination', function (): void {
    Post::factory()->count(15)->create();

    $html = livewireTableWithParts([TablePaginationOverview::make()])->html();

    expect($html)
        ->toContain('<div class="fi-pagination-part">')
        ->toContain('Showing 1 to 10 of 15 results')
        ->not->toContain('fi-pagination"');
});

it('renders nothing for a simple paginator', function (): void {
    Post::factory()->count(15)->create();

    $html = livewireTableWithParts(
        [TablePaginationOverview::make()],
        fn (Table $table): Table => $table->paginationMode(PaginationMode::Simple),
    )->html();

    expect($html)->not->toContain('fi-pagination-overview');
});

it('renders nothing when `paginated(false)`', function (): void {
    Post::factory()->count(15)->create();

    $html = livewireTableWithParts(
        [TablePaginationOverview::make()],
        fn (Table $table): Table => $table->paginated(false),
    )->html();

    expect($html)->not->toContain('fi-pagination');
});
