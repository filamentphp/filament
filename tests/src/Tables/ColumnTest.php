<?php

use Filament\Tests\Models\Post;
use Filament\Tests\Tables\Fixtures\PostsTable;
use Filament\Tests\Tables\TestCase;
use function Pest\Livewire\livewire;

uses(TestCase::class);

it('can render text column', function () {
    Post::factory()->count(10)->create();

    livewire(PostsTable::class)
        ->assertCanRenderTableColumn('title');
});

it('can render text column with relationship', function () {
    Post::factory()->count(10)->create();

    livewire(PostsTable::class)
        ->assertCanRenderTableColumn('author.name');
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

    livewire(PostsTable::class)
        ->searchTable($title)
        ->assertCanSeeTableRecords($posts->where('title', $title))
        ->assertCanNotSeeTableRecords($posts->where('title', '!=', $title));
});

it('can search individual column records', function () {
    $posts = Post::factory()->count(10)->create();

    $content = $posts->first()->content;

    livewire(PostsTable::class)
        ->searchTableColumns(['content' => $content])
        ->assertCanSeeTableRecords($posts->where('content', $content))
        ->assertCanNotSeeTableRecords($posts->where('content', '!=', $content));
});

it('can search posts with relationship', function () {
    $posts = Post::factory()->count(10)->create();

    $author = $posts->first()->author->name;

    livewire(PostsTable::class)
        ->searchTable($author)
        ->assertCanSeeTableRecords($posts->where('author.name', $author))
        ->assertCanNotSeeTableRecords($posts->where('author.name', '!=', $author));
});

it('can search individual column records with relationship', function () {
    $posts = Post::factory()->count(10)->create();

    $authorEmail = $posts->first()->author->email;

    livewire(PostsTable::class)
        ->searchTableColumns(['author.email' => $authorEmail])
        ->assertCanSeeTableRecords($posts->where('author.email', $authorEmail))
        ->assertCanNotSeeTableRecords($posts->where('author.email', '!=', $authorEmail));
});

it('can search multiple individual columns', function () {
    $posts = Post::factory()->count(10)->create();

    $content = $posts->first()->content;
    $authorEmail = $posts->first()->author->email;

    livewire(PostsTable::class)
        ->searchTableColumns([
            'content' => $content,
            'author.email' => $authorEmail,
        ])
        ->assertCanSeeTableRecords($posts->where('author.email', $authorEmail))
        ->assertCanNotSeeTableRecords($posts->where('author.email', '!=', $authorEmail));
});

it('can hide a column', function () {
    livewire(PostsTable::class)
        ->assertTableColumnVisible('visible')
        ->assertTableColumnHidden('hidden');
});

it('can call a column action', function () {
    $post = Post::factory()->create();

    livewire(PostsTable::class)
        ->callTableColumnAction('title', $post)
        ->assertEmitted('title-action-called');
});

it('can call a column action object', function () {
    $post = Post::factory()->create();

    livewire(PostsTable::class)
        ->callTableAction('column-action-object', $post)
        ->assertEmitted('column-action-object-called');
});

it('can state whether a column has the correct value', function () {
    $post = Post::factory()->create();

    livewire(PostsTable::class)
        ->assertTableColumnStateSet('with_state', 'correct state', $post)
        ->assertTableColumnStateNotSet('with_state', 'incorrect state', $post);
});

it('can state whether a column has the correct formatted value', function () {
    $post = Post::factory()->create();

    livewire(PostsTable::class)
        ->assertTableColumnFormattedStateSet('formatted_state', 'formatted state', $post)
        ->assertTableColumnFormattedStateNotSet('formatted_state', 'incorrect formatted state', $post);
});

it('can state whether a column has extra attributes', function () {
    $post = Post::factory()->create();

    livewire(PostsTable::class)
        ->assertTableColumnHasExtraAttributes('extra_attributes', ['class' => 'text-danger-500'], $post)
        ->assertTableColumnDoesNotHaveExtraAttributes('extra_attributes', ['class' => 'text-primary-500'], $post);
});

it('can state whether a column has a description', function () {
    $post = Post::factory()->create();

    livewire(PostsTable::class)
        ->assertTableColumnHasDescription('with_description', 'description below', $post)
        ->assertTableColumnHasDescription('with_description', 'description above', $post, 'above')
        ->assertTableColumnDoesNotHaveDescription('with_description', 'incorrect description below', $post)
        ->assertTableColumnDoesNotHaveDescription('with_description', 'incorrect description above', $post, 'above');
});

it('can state whether a select column has options', function () {
    $post = Post::factory()->create();

    livewire(PostsTable::class)
        ->assertSelectColumnHasOptions('with_options', ['red' => 'Red', 'blue' => 'Blue'], $post)
        ->assertSelectColumnDoesNotHaveOptions('with_options', ['one' => 'One', 'two' => 'Two'], $post);
});
