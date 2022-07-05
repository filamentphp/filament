<?php

use Filament\Tests\Models\Post;
use Filament\Tests\Tables\Fixtures\PostsTable;
use Filament\Tests\Tables\TestCase;
use function Pest\Livewire\livewire;

uses(TestCase::class);

it('can list records', function () {
    $posts = Post::factory()->count(10)->create();

    livewire(PostsTable::class)
        ->assertCanSeeTableRecords($posts);
});

it('can sort records', function () {
    $posts = Post::factory()->count(10)->create();

    livewire(PostsTable::class)
        ->sortTable('title')
        ->assertCanSeeTableRecords($posts->sortBy('title'), inOrder: true)
        ->sortTable('title', 'desc')
        ->assertCanSeeTableRecords($posts->sortByDesc('title'), inOrder: true);
});

it('can sort records with relationship', function () {
    $posts = Post::factory()->count(10)->create();

    livewire(PostsTable::class)
        ->sortTable('author.name')
        ->assertCanSeeTableRecords($posts->sortBy('author.name'), inOrder: true)
        ->sortTable('author.name', 'desc')
        ->assertCanSeeTableRecords($posts->sortByDesc('author.name'), inOrder: true);
});

it('can search records', function () {
    $posts = Post::factory()->count(10)->create();

    $title = $posts->first()->title;

    $searchedPosts = $posts->where('title', $title);

    livewire(PostsTable::class)
        ->searchTable($title)
        ->assertCanSeeTableRecords($searchedPosts)
        ->assertCountTableRecords($searchedPosts->count())
        ->assertCanNotSeeTableRecords($posts->where('title', '!=', $title));
});

it('can search posts with relationship', function () {
    $posts = Post::factory()->count(10)->create();

    $author = $posts->first()->author->name;

    $searchedPosts = $posts->where('author.name', $author);

    livewire(PostsTable::class)
        ->searchTable($author)
        ->assertCanSeeTableRecords($searchedPosts)
        ->assertCountTableRecords($searchedPosts->count())
        ->assertCanNotSeeTableRecords($posts->where('author.name', '!=', $author));
});
