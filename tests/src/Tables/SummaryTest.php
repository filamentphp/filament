<?php

use Filament\Tests\Fixtures\Livewire\PostsTable;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Tables\TestCase;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can average values in a column', function (): void {
    $posts = Post::factory()->count(20)->create();

    livewire(PostsTable::class)
        ->assertCanSeeTableRecords($posts->take(10))
        ->assertTableColumnSummarySet('rating', 'average', $posts->avg('rating'));
});

it('can average values in a column on this pagination page', function (): void {
    $posts = Post::factory()->count(20)->create();

    livewire(PostsTable::class)
        ->assertCanSeeTableRecords($posts->take(10))
        ->assertTableColumnSummarySet('rating', 'average', $posts->take(10)->avg('rating'), isCurrentPaginationPageOnly: true);
});

it('can average subset of values in a column', function (): void {
    $posts = Post::factory()->count(20)->create();

    livewire(PostsTable::class)
        ->assertCanSeeTableRecords($posts->take(10))
        ->assertTableColumnSummarySet('rating', 'published_average', $posts->where('is_published', true)->avg('rating'));
});

it('can average subset of values in a column on this pagination page', function (): void {
    $posts = Post::factory()->count(20)->create();

    livewire(PostsTable::class)
        ->assertCanSeeTableRecords($posts->take(10))
        ->assertTableColumnSummarySet('rating', 'published_average', $posts->take(10)->where('is_published', true)->avg('rating'), isCurrentPaginationPageOnly: true);
});

it('can count rows', function (): void {
    $posts = Post::factory()->count(20)->create();

    livewire(PostsTable::class)
        ->assertCanSeeTableRecords($posts->take(10))
        ->assertTableColumnSummarySet('rating', 'count', $posts->count());
});

it('can count rows on this pagination page', function (): void {
    $posts = Post::factory()->count(20)->create();

    livewire(PostsTable::class)
        ->assertCanSeeTableRecords($posts->take(10))
        ->assertTableColumnSummarySet('rating', 'count', $posts->take(10)->count(), isCurrentPaginationPageOnly: true);
});

it('can count subset of rows', function (): void {
    $posts = Post::factory()->count(20)->create();

    livewire(PostsTable::class)
        ->assertCanSeeTableRecords($posts->take(10))
        ->assertTableColumnSummarySet('is_published', 'published_count', $posts->where('is_published', true)->count());
});

it('can count subset of rows on this pagination page', function (): void {
    $posts = Post::factory()->count(20)->create();

    livewire(PostsTable::class)
        ->assertCanSeeTableRecords($posts->take(10))
        ->assertTableColumnSummarySet('is_published', 'published_count', $posts->take(10)->where('is_published', true)->count(), isCurrentPaginationPageOnly: true);
});

it('can get the range of values in a column', function (): void {
    $posts = Post::factory()->count(20)->create();

    livewire(PostsTable::class)
        ->assertCanSeeTableRecords($posts->take(10))
        ->assertTableColumnSummarySet('rating', 'range', [$posts->min('rating'), $posts->max('rating')]);
});

it('can get the range of values in a column on this pagination page', function (): void {
    $posts = Post::factory()->count(20)->create();

    livewire(PostsTable::class)
        ->assertCanSeeTableRecords($posts->take(10))
        ->assertTableColumnSummarySet('rating', 'range', [$posts->take(10)->min('rating'), $posts->take(10)->max('rating')], isCurrentPaginationPageOnly: true);
});

it('can get the range of a subset of values in a column', function (): void {
    $posts = Post::factory()->count(20)->create();

    livewire(PostsTable::class)
        ->assertCanSeeTableRecords($posts->take(10))
        ->assertTableColumnSummarySet('rating', 'published_range', [$posts->where('is_published', true)->min('rating'), $posts->where('is_published', true)->max('rating')]);
});

it('can get the range of a subset of values in a column on this pagination page', function (): void {
    $posts = Post::factory()->count(20)->create();

    livewire(PostsTable::class)
        ->assertCanSeeTableRecords($posts->take(10))
        ->assertTableColumnSummarySet('rating', 'published_range', [$posts->take(10)->where('is_published', true)->min('rating'), $posts->take(10)->where('is_published', true)->max('rating')], isCurrentPaginationPageOnly: true);
});

it('can get the range of values from a relationship in a column', function (): void {
    $posts = Post::factory()->count(20)->create();

    livewire(PostsTable::class)
        ->assertCanSeeTableRecords($posts->take(10))
        ->assertTableColumnSummarySet('author.name', 'range', [$posts->min('author.name'), $posts->max('author.name')]);
});

it('can get the range of values from a relationship in a column on this pagination page', function (): void {
    $posts = Post::factory()->count(20)->create();

    livewire(PostsTable::class)
        ->assertCanSeeTableRecords($posts->take(10))
        ->assertTableColumnSummarySet('author.name', 'range', [$posts->take(10)->min('author.name'), $posts->take(10)->max('author.name')], isCurrentPaginationPageOnly: true);
});

it('can sum values in a column', function (): void {
    $posts = Post::factory()->count(20)->create();

    livewire(PostsTable::class)
        ->assertCanSeeTableRecords($posts->take(10))
        ->assertTableColumnSummarySet('rating', 'sum', $posts->sum('rating'));
});

it('can sum values in a column on this pagination page', function (): void {
    $posts = Post::factory()->count(20)->create();

    livewire(PostsTable::class)
        ->assertCanSeeTableRecords($posts->take(10))
        ->assertTableColumnSummarySet('rating', 'sum', $posts->take(10)->sum('rating'), isCurrentPaginationPageOnly: true);
});

it('can sum subset of values in a column', function (): void {
    $posts = Post::factory()->count(20)->create();

    livewire(PostsTable::class)
        ->assertCanSeeTableRecords($posts->take(10))
        ->assertTableColumnSummarySet('rating', 'published_sum', $posts->where('is_published', true)->sum('rating'));
});

it('can sum subset of values in a column on this pagination page', function (): void {
    $posts = Post::factory()->count(20)->create();

    livewire(PostsTable::class)
        ->assertCanSeeTableRecords($posts->take(10))
        ->assertTableColumnSummarySet('rating', 'published_sum', $posts->take(10)->where('is_published', true)->sum('rating'), isCurrentPaginationPageOnly: true);
});
