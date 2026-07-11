<?php

use Filament\Tests\Fixtures\Livewire\PostsTable;
use Filament\Tests\Fixtures\Livewire\PostsTableWithFilterPlacements;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Tables\TestCase;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('renders the above-content placement container and both filters drive the table', function (): void {
    $publishedPosts = Post::factory()->count(6)->create(['is_published' => true]);
    $unpublishedPosts = Post::factory()->count(4)->create(['is_published' => false]);

    // `is_published` is placed AboveContent (always visible); `author` stays in the Dropdown.
    livewire(PostsTableWithFilterPlacements::class)
        ->assertSeeHtml('fi-ta-filters-above-content-ctn')
        ->assertCanSeeTableRecords($publishedPosts->merge($unpublishedPosts))
        ->filterTable('is_published')
        ->assertCanSeeTableRecords($publishedPosts)
        ->assertCanNotSeeTableRecords($unpublishedPosts);
});

it('renders a default (single-placement) table identically, in the dropdown, not above content', function (): void {
    Post::factory()->count(3)->create();

    livewire(PostsTable::class)
        ->assertDontSeeHtml('fi-ta-filters-above-content-ctn')
        ->assertSeeHtml('fi-ta-filters-dropdown');
});
