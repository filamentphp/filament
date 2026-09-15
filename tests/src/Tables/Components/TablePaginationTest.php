<?php

use Filament\Tables\Components\TablePagination;
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
