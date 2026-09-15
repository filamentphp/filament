<?php

use Filament\Tables\Components\TablePaginationRecordsPerPage;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Tables\TestCase;

use function Filament\Tests\livewireTableWithParts;

uses(TestCase::class);

it('renders the per page select in its own wrapper when placed outside the pagination', function (): void {
    Post::factory()->count(15)->create();

    $html = livewireTableWithParts([TablePaginationRecordsPerPage::make()])->html();

    expect($html)
        ->toContain('<div class="fi-pagination-part">')
        ->toContain('fi-pagination-records-per-page-select-ctn')
        ->toContain('wire:model.live="tableRecordsPerPage"')
        ->not->toContain('fi-pagination"');
});

it('renders nothing with a single page option', function (): void {
    Post::factory()->count(15)->create();

    $html = livewireTableWithParts(
        [TablePaginationRecordsPerPage::make()],
        fn (Table $table): Table => $table->paginationPageOptions([10]),
    )->html();

    expect($html)->not->toContain('fi-pagination-records-per-page-select-ctn');
});
