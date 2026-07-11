<?php

use Filament\Tests\Fixtures\Livewire\PostsTable;
use Filament\Tests\Fixtures\Livewire\PostsTableWithFilterPanels;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Tables\TestCase;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('renders each panel in its location and both drive the table', function (): void {
    $posts = Post::factory()->count(10)->create();

    // `is_published` is in the AboveContent panel; `author` in the Dropdown panel.
    livewire(PostsTableWithFilterPanels::class)
        ->assertSeeHtml('fi-ta-filters-above-content-ctn')
        ->assertSeeHtml('fi-ta-filters-dropdown')
        ->assertCanSeeTableRecords($posts)
        ->filterTable('is_published')
        ->assertCanSeeTableRecords($posts->where('is_published', true))
        ->assertCanNotSeeTableRecords($posts->where('is_published', false));
});

it('renders a flat table in the dropdown only, not above content', function (): void {
    Post::factory()->count(3)->create();

    livewire(PostsTable::class)
        ->assertDontSeeHtml('fi-ta-filters-above-content-ctn')
        ->assertSeeHtml('fi-ta-filters-dropdown');
});
