<?php

use Filament\Tables\Components\TablePagination;
use Filament\Tables\Components\TablePaginationLinks;
use Filament\Tables\Components\TablePaginationOverview;
use Filament\Tables\Enums\PaginationMode;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Tables\TestCase;

use function Filament\Tests\livewireTableWithParts;

uses(TestCase::class);

it('renders the pagination for a paginated table', function (): void {
    Post::factory()->count(15)->create();

    $html = livewireTableWithParts([TablePagination::make()])->html();

    expect($html)->toContain('fi-pagination-next-btn');
});

it('renders nothing when `paginated(false)`', function (): void {
    Post::factory()->count(15)->create();

    $html = livewireTableWithParts(
        [TablePagination::make()],
        fn (Table $table): Table => $table->paginated(false),
    )->html();

    expect($html)->not->toContain('fi-pagination');
});

it('renders nothing before a deferred table is loaded', function (): void {
    Post::factory()->count(15)->create();

    $html = livewireTableWithParts(
        [TablePagination::make()],
        fn (Table $table): Table => $table->deferLoading(),
    )->html();

    expect($html)->not->toContain('fi-pagination');
});

it('renders the overview, the per page select and the links by default', function (): void {
    Post::factory()->count(15)->create();

    $html = livewireTableWithParts([TablePagination::make()])->html();

    expect($html)
        ->toContain('class="fi-pagination"')
        ->not->toContain('fi-ta-pagination')
        ->toContain('fi-pagination-overview')
        ->toContain('fi-pagination-records-per-page-select-ctn')
        ->toContain('fi-pagination-items')
        ->not->toContain('fi-pagination-links');
});

it('renders only the given parts in a composed pagination', function (): void {
    Post::factory()->count(15)->create();

    $html = livewireTableWithParts([
        TablePagination::make([
            TablePaginationOverview::make(),
            TablePaginationLinks::make(),
        ]),
    ])->html();

    expect($html)
        ->toContain('class="fi-pagination fi-ta-pagination"')
        ->toContain('fi-pagination-overview')
        ->toContain('class="fi-pagination-links"')
        ->toContain('fi-pagination-items')
        ->not->toContain('fi-pagination-records-per-page-select-ctn')
        ->not->toContain('fi-pagination-part');
});

it('marks a composed pagination as simple for a simple paginator', function (): void {
    Post::factory()->count(15)->create();

    $html = livewireTableWithParts(
        [TablePagination::make([TablePaginationLinks::make()])],
        fn (Table $table): Table => $table->paginationMode(PaginationMode::Simple),
    )->html();

    expect($html)
        ->toContain('class="fi-pagination fi-ta-pagination fi-simple"')
        ->toContain('fi-pagination-next-btn')
        ->not->toContain('fi-pagination-items');
});
