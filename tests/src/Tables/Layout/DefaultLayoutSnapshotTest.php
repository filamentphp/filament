<?php

use Filament\Tests\Fixtures\Livewire\DefaultGroupedPostsTable;
use Filament\Tests\Fixtures\Livewire\PostsColumnManagerTable;
use Filament\Tests\Fixtures\Livewire\PostsReorderableTable;
use Filament\Tests\Fixtures\Livewire\PostsTable;
use Filament\Tests\Fixtures\Livewire\PostsTableWithAboveContentCollapsibleFilters;
use Filament\Tests\Fixtures\Livewire\PostsTableWithAboveContentFilters;
use Filament\Tests\Fixtures\Livewire\PostsTableWithAfterContentFilters;
use Filament\Tests\Fixtures\Livewire\PostsTableWithBeforeContentFilters;
use Filament\Tests\Fixtures\Livewire\PostsTableWithBelowContentFilters;
use Filament\Tests\Fixtures\Livewire\PostsTableWithContentGrid;
use Filament\Tests\Fixtures\Livewire\PostsTableWithDeferredLoading;
use Filament\Tests\Fixtures\Livewire\PostsTableWithHiddenFilters;
use Filament\Tests\Fixtures\Livewire\PostsTableWithModalFilters;
use Filament\Tests\Fixtures\Livewire\SelectablePostsTable;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Tables\TestCase;

use function Filament\Tests\livewire;
use function Filament\Tests\normalizeTableHtml;

uses(TestCase::class);

/**
 * Snapshots need identical data on every run, so the records are created with fixed attributes instead of Faker output.
 */
function seedPostsForSnapshot(): void
{
    $author = User::factory()->create([
        'name' => 'Dan Harrin',
        'email' => 'dan@filamentphp.com',
        'remember_token' => 'remember-token',
    ]);

    foreach (['Alpha', 'Bravo', 'Charlie'] as $index => $title) {
        Post::factory()->create([
            'author_id' => $author,
            'content' => "{$title} content",
            'is_published' => ($index % 2) === 0,
            'rating' => $index + 1,
            'sort' => $index,
            'tags' => ['tag'],
            'title' => "{$title} post",
        ]);
    }
}

function renderTableForSnapshot(string $component): string
{
    $livewire = livewire($component);

    return normalizeTableHtml($livewire->html(), $livewire->instance()->getId());
}

it('renders the default layout', function (string $component): void {
    seedPostsForSnapshot();

    expect(renderTableForSnapshot($component))->toMatchSnapshot();
})->with([
    'plain' => PostsTable::class,
    'selectable' => SelectablePostsTable::class,
    'reorderable' => PostsReorderableTable::class,
    'filters above content' => PostsTableWithAboveContentFilters::class,
    'filters above content collapsible' => PostsTableWithAboveContentCollapsibleFilters::class,
    'filters below content' => PostsTableWithBelowContentFilters::class,
    'filters before content' => PostsTableWithBeforeContentFilters::class,
    'filters after content' => PostsTableWithAfterContentFilters::class,
    'filters modal' => PostsTableWithModalFilters::class,
    'filters hidden' => PostsTableWithHiddenFilters::class,
    'content grid' => PostsTableWithContentGrid::class,
    'column manager' => PostsColumnManagerTable::class,
    'deferred loading' => PostsTableWithDeferredLoading::class,
    'grouped' => DefaultGroupedPostsTable::class,
]);

it('renders the default layout without records', function (): void {
    expect(renderTableForSnapshot(PostsTable::class))->toMatchSnapshot();
});
