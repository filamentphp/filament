<?php

use Filament\Tests\Models\Comment;
use Filament\Tests\Models\Post;
use Filament\Tests\Models\User;
use Filament\Tests\Tables\Fixtures\CommentsTable;
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

it('can render text column with deep relationship', function () {
    $posts = Post::factory()->count(5)->create();

    foreach ($posts as $post) {
        $post->comments()->createMany(
            Comment::factory()->count(10)->make()->toArray()
        );
    }

    livewire(PostsTable::class)
        ->assertCanRenderTableColumn('comments.author.name');
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

it('can sort records with deep relationship', function () {
    $posts = Post::factory()->count(10)->create();

    foreach ($posts as $post) {
        $post->comments()->create(
            Comment::factory()->make()->toArray()
        );
    }

    $comments = Comment::all();

    livewire(CommentsTable::class)
        ->sortTable('post.author.name')
        ->assertCanSeeTableRecords($comments->sortBy(function ($comment) {
            return $comment->post->author->name;
        }), inOrder: true)
        ->sortTable('post.author.name', 'desc')
        ->assertCanSeeTableRecords($comments->sortByDesc(function ($comment) {
            return $comment->post->author->name;
        }), inOrder: true);
});

it('can search records', function () {
    $posts = Post::factory()->count(10)->create();

    $title = $posts->first()->title;

    livewire(PostsTable::class)
        ->searchTable($title)
        ->assertCanSeeTableRecords($posts->where('title', $title))
        ->assertCanNotSeeTableRecords($posts->where('title', '!=', $title));
});

it('can search posts with relationship', function () {
    $posts = Post::factory()->count(10)->create();

    $author = $posts->first()->author->name;

    livewire(PostsTable::class)
        ->searchTable($author)
        ->assertCanSeeTableRecords($posts->where('author.name', $author))
        ->assertCanNotSeeTableRecords($posts->where('author.name', '!=', $author));
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

it('can set a select column value', function () {
    $post = Post::factory()->create(['with_options' => 'blue']);

    livewire(PostsTable::class)
        ->assertSelectColumnHasOptions('with_options', ['red' => 'Red', 'blue' => 'Blue'], $post)
        ->assertTableColumnStateSet('with_options', 'blue', $post)
        ->setColumnValue('with_options', $post, 'red');

    $post = Post::first();

    livewire(PostsTable::class)
        ->assertSelectColumnHasOptions('with_options', ['red' => 'Red', 'blue' => 'Blue'], $post)
        ->assertTableColumnStateSet('with_options', 'red', $post);
});

it('can set a distant select column value', function () {
    $post = Post::factory()->create();

    $post->comments()->create(
        Comment::factory()->make()->toArray()
    );

    $comment = $post->comments->first();
    $user = User::factory()->create();

    livewire(CommentsTable::class)
        ->setColumnValue('post.author_id', $comment, $user->id);

    $comment = Comment::first();

    livewire(CommentsTable::class)
        ->assertTableColumnStateSet('post.author_id', $user->id, $comment);
});
