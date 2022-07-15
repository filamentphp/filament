<?php

use Filament\Tests\Models\Post;
use Filament\Tests\Tables\Fixtures\PostsTable;
use Filament\Tests\Tables\TestCase;
use function Pest\Livewire\livewire;

uses(TestCase::class);

it('can filter records', function () {
    $posts = Post::factory()->count(10)->create();

    livewire(PostsTable::class)
        ->assertCanSeeTableRecords($posts)
        ->filterTable('is_published')
        ->assertCanSeeTableRecords($posts->where('is_published', true))
        ->assertCanNotSeeTableRecords($posts->where('is_published', false));
});

it('can reset filters', function () {
    $posts = Post::factory()->count(10)->create();

    $unpublishedPosts = $posts->where('is_published', false);

    livewire(PostsTable::class)
        ->filterTable('is_published')
        ->assertCanNotSeeTableRecords($unpublishedPosts)
        ->resetTableFilters()
        ->assertCanSeeTableRecords($unpublishedPosts);
});
