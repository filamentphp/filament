<?php

use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Tables\Fixtures\PostsTableWithHeaderFilters;
use Filament\Tests\Tables\TestCase;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can filter records using a header filter', function (): void {
    $posts = Post::factory()->count(10)->create();

    livewire(PostsTableWithHeaderFilters::class)
        ->assertCanSeeTableRecords($posts)
        ->set('tableFilters.is_published.isActive', true)
        ->assertCanSeeTableRecords($posts->where('is_published', true))
        ->assertCanNotSeeTableRecords($posts->where('is_published', false));
});

it('can filter records using a header `SelectFilter`', function (): void {
    $posts = Post::factory()->count(10)->create();

    $author = $posts->first()->author;

    livewire(PostsTableWithHeaderFilters::class)
        ->assertCanSeeTableRecords($posts)
        ->set('tableFilters.author.value', $author->getKey())
        ->assertCanSeeTableRecords($posts->where('author_id', $author->getKey()))
        ->assertCanNotSeeTableRecords($posts->where('author_id', '!=', $author->getKey()));
});

it('can reset header filters with `resetTableFiltersForm()`', function (): void {
    $posts = Post::factory()->count(10)->create();

    livewire(PostsTableWithHeaderFilters::class)
        ->assertCanSeeTableRecords($posts)
        ->set('tableFilters.is_published.isActive', true)
        ->assertCanNotSeeTableRecords($posts->where('is_published', false))
        ->call('resetTableFiltersForm')
        ->assertCanSeeTableRecords($posts);
});

it('uses the same state property as panel filters', function (): void {
    $posts = Post::factory()->count(10)->create();

    livewire(PostsTableWithHeaderFilters::class)
        ->assertCanSeeTableRecords($posts)
        ->set('tableFilters.is_published.isActive', true)
        ->assertSet('tableFilters.is_published.isActive', true);
});
