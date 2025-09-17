<?php

use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tests\Fixtures\Livewire\PostsTable;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Tables\TestCase;
use Illuminate\Support\Str;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can render text column', function (): void {
    Post::factory()->count(10)->create();

    livewire(PostsTable::class)
        ->assertCanRenderTableColumn('title');
});

it('can render text column with relationship', function (): void {
    Post::factory()->count(10)->create();

    livewire(PostsTable::class)
        ->assertCanRenderTableColumn('author.name');
});

it('can sort records', function (): void {
    $posts = Post::factory()->count(10)->create();

    livewire(PostsTable::class)
        ->sortTable('title')
        ->assertCanSeeTableRecords($posts->sortBy('title'), inOrder: true)
        ->sortTable('title', 'desc')
        ->assertCanSeeTableRecords($posts->sortByDesc('title'), inOrder: true);
});

it('can sort records with relationship', function (): void {
    $posts = Post::factory()->count(10)->create();

    livewire(PostsTable::class)
        ->sortTable('author.name')
        ->assertCanSeeTableRecords($posts->sortBy('author.name'), inOrder: true)
        ->sortTable('author.name', 'desc')
        ->assertCanSeeTableRecords($posts->sortByDesc('author.name'), inOrder: true);
});

it('can sort records with JSON column', function (): void {
    $posts = Post::factory()->count(10)->state(fn (): array => [
        'json' => ['foo' => Str::random()],
    ])->create();

    livewire(PostsTable::class)
        ->sortTable('json.foo')
        ->assertCanSeeTableRecords($posts->sortBy('json.foo'), inOrder: true)
        ->sortTable('json.foo', 'desc')
        ->assertCanSeeTableRecords($posts->sortByDesc('json.foo'), inOrder: true);
});

it('can sort records with nested JSON column', function (): void {
    $posts = Post::factory()->count(10)->state(fn (): array => [
        'json' => ['bar' => ['baz' => Str::random()]],
    ])->create();

    livewire(PostsTable::class)
        ->sortTable('json.bar.baz')
        ->assertCanSeeTableRecords($posts->sortBy('json.bar.baz'), inOrder: true)
        ->sortTable('json.bar.baz', 'desc')
        ->assertCanSeeTableRecords($posts->sortByDesc('json.bar.baz'), inOrder: true);
});

it('can sort records with relationship JSON column', function (): void {
    $posts = Post::factory()->count(10)->state(fn (): array => [
        'author_id' => User::factory()->state(['json' => ['foo' => Str::random()]]),
    ])->create();

    livewire(PostsTable::class)
        ->sortTable('author.json.foo')
        ->assertCanSeeTableRecords($posts->sortBy('author.json.foo'), inOrder: true)
        ->sortTable('author.json.foo', 'desc')
        ->assertCanSeeTableRecords($posts->sortByDesc('author.json.foo'), inOrder: true);
});

it('can sort records with relationship nested JSON column', function (): void {
    $posts = Post::factory()->count(10)->state(fn (): array => [
        'author_id' => User::factory()->state(['json' => ['bar' => ['baz' => Str::random()]]]),
    ])->create();

    livewire(PostsTable::class)
        ->sortTable('author.json.bar.baz')
        ->assertCanSeeTableRecords($posts->sortBy('author.json.bar.baz'), inOrder: true)
        ->sortTable('author.json.bar.baz', 'desc')
        ->assertCanSeeTableRecords($posts->sortByDesc('author.json.bar.baz'), inOrder: true);
});

it('can search records', function (): void {
    $posts = Post::factory()->count(10)->create();

    $title = $posts->first()->title;

    livewire(PostsTable::class)
        ->searchTable($title)
        ->assertCanSeeTableRecords($posts->where('title', $title))
        ->assertCanNotSeeTableRecords($posts->where('title', '!=', $title));
});

it('can search individual column records', function (): void {
    $posts = Post::factory()->count(10)->create();

    $content = $posts->first()->content;

    livewire(PostsTable::class)
        ->searchTableColumns(['content' => $content])
        ->assertCanSeeTableRecords($posts->where('content', $content))
        ->assertCanNotSeeTableRecords($posts->where('content', '!=', $content));
});

it('can search posts with relationship', function (): void {
    $posts = Post::factory()->count(10)->create();

    $author = $posts->first()->author->name;

    livewire(PostsTable::class)
        ->searchTable($author)
        ->assertCanSeeTableRecords($posts->where('author.name', $author))
        ->assertCanNotSeeTableRecords($posts->where('author.name', '!=', $author));
});

it('can search posts with JSON column', function (): void {
    $search = Str::random();

    $matchingPosts = Post::factory()->count(5)->create([
        'json' => ['foo' => $search],
    ]);

    $notMatchingPosts = Post::factory()->count(5)->create([
        'json' => ['foo' => Str::random()],
    ]);

    livewire(PostsTable::class)
        ->searchTable($search)
        ->assertCanSeeTableRecords($matchingPosts)
        ->assertCanNotSeeTableRecords($notMatchingPosts);
});

it('can search posts with nested JSON column', function (): void {
    $search = Str::random();

    $matchingPosts = Post::factory()->count(5)->create([
        'json' => ['bar' => ['baz' => $search]],
    ]);

    $notMatchingPosts = Post::factory()->count(5)->create([
        'json' => ['bar' => ['baz' => Str::random()]],
    ]);

    livewire(PostsTable::class)
        ->searchTable($search)
        ->assertCanSeeTableRecords($matchingPosts)
        ->assertCanNotSeeTableRecords($notMatchingPosts);
});

it('can search posts with relationship JSON column', function (): void {
    $search = Str::random();

    $matchingAuthor = User::factory()
        ->create(['json' => ['foo' => $search]]);

    $matchingPosts = Post::factory()
        ->for($matchingAuthor, 'author')
        ->count(5)
        ->create();

    $notMatchingPosts = Post::factory()
        ->count(5)
        ->create();

    livewire(PostsTable::class)
        ->searchTable($search)
        ->assertCanSeeTableRecords($matchingPosts)
        ->assertCanNotSeeTableRecords($notMatchingPosts);
});

it('can search posts with relationship nested JSON column', function (): void {
    $search = Str::random();

    $matchingAuthor = User::factory()
        ->create(['json' => ['bar' => ['baz' => $search]]]);

    $matchingPosts = Post::factory()
        ->for($matchingAuthor, 'author')
        ->count(5)
        ->create();

    $notMatchingPosts = Post::factory()
        ->count(5)
        ->create();

    livewire(PostsTable::class)
        ->searchTable($search)
        ->assertCanSeeTableRecords($matchingPosts)
        ->assertCanNotSeeTableRecords($notMatchingPosts);
});

it('can search individual column records with relationship', function (): void {
    $posts = Post::factory()->count(10)->create();

    $authorEmail = $posts->first()->author->email;

    livewire(PostsTable::class)
        ->searchTableColumns(['author.email' => $authorEmail])
        ->assertCanSeeTableRecords($posts->where('author.email', $authorEmail))
        ->assertCanNotSeeTableRecords($posts->where('author.email', '!=', $authorEmail));
});

it('can search multiple individual columns', function (): void {
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

it('can hide a column', function (): void {
    livewire(PostsTable::class)
        ->assertTableColumnVisible('visible')
        ->assertTableColumnHidden('hidden');
});

it('can call a column action', function (): void {
    $post = Post::factory()->create();

    livewire(PostsTable::class)
        ->callTableColumnAction('title', $post)
        ->assertDispatched('title-action-called');
});

it('can call a column action object', function (): void {
    $post = Post::factory()->create();

    livewire(PostsTable::class)
        ->callTableAction('column-action-object', $post)
        ->assertDispatched('column-action-object-called');
});

it('can state whether a column has the correct value', function (): void {
    $post = Post::factory()->create();

    livewire(PostsTable::class)
        ->assertTableColumnStateSet('with_state', 'correct state', $post)
        ->assertTableColumnStateNotSet('with_state', 'incorrect state', $post);
});

it('can state whether a column has the correct formatted value', function (): void {
    $post = Post::factory()->create();

    livewire(PostsTable::class)
        ->assertTableColumnFormattedStateSet('formatted_state', 'formatted state', $post)
        ->assertTableColumnFormattedStateNotSet('formatted_state', 'incorrect formatted state', $post);
});

it('can output JSON values', function (): void {
    $post = Post::factory()->create([
        'json' => ['foo' => 'bar'],
    ]);

    livewire(PostsTable::class)
        ->assertTableColumnStateSet('json.foo', 'bar', $post);
});

it('can output nested JSON values', function (): void {
    $post = Post::factory()->create([
        'json' => ['bar' => ['baz' => 'qux']],
    ]);

    livewire(PostsTable::class)
        ->assertTableColumnStateSet('json.bar.baz', 'qux', $post);
});

it('can output relationship JSON values', function (): void {
    $post = Post::factory()->create([
        'author_id' => User::factory()->state([
            'json' => ['foo' => 'bar'],
        ]),
    ]);

    livewire(PostsTable::class)
        ->assertTableColumnStateSet('author.json.foo', 'bar', $post);
});

it('can output relationship nested JSON values', function (): void {
    $post = Post::factory()->create([
        'author_id' => User::factory()->state([
            'json' => ['bar' => ['baz' => 'qux']],
        ]),
    ]);

    livewire(PostsTable::class)
        ->assertTableColumnStateSet('author.json.bar.baz', 'qux', $post);
});

it('can output values in a JSON array column of objects', function (): void {
    $post = Post::factory()->create([
        'json_array_of_objects' => [
            ['value' => 'foo'],
            ['value' => 'bar'],
            ['value' => 'baz'],
        ],
    ]);

    livewire(PostsTable::class)
        ->assertTableColumnStateSet('json_array_of_objects.*.value', ['foo', 'bar', 'baz'], $post);
});

it('can state whether a column has extra attributes', function (): void {
    $post = Post::factory()->create();

    livewire(PostsTable::class)
        ->assertTableColumnHasExtraAttributes('extra_attributes', ['class' => 'text-danger-500'], $post)
        ->assertTableColumnDoesNotHaveExtraAttributes('extra_attributes', ['class' => 'text-primary-500'], $post);
});

it('can state whether a column has a description', function (): void {
    $post = Post::factory()->create();

    livewire(PostsTable::class)
        ->assertTableColumnHasDescription('with_description', 'description below', $post)
        ->assertTableColumnHasDescription('with_description', 'description above', $post, 'above')
        ->assertTableColumnDoesNotHaveDescription('with_description', 'incorrect description below', $post)
        ->assertTableColumnDoesNotHaveDescription('with_description', 'incorrect description above', $post, 'above');
});

it('can state whether a select column has options', function (): void {
    $post = Post::factory()->create();

    livewire(PostsTable::class)
        ->assertTableSelectColumnHasOptions('with_options', ['red' => 'Red', 'blue' => 'Blue'], $post)
        ->assertTableSelectColumnDoesNotHaveOptions('with_options', ['one' => 'One', 'two' => 'Two'], $post);
});

it('can assert that a column exists with the given configuration', function (): void {
    $publishedPost = Post::factory()->create([
        'is_published' => true,
    ]);

    livewire(PostsTable::class)
        ->assertTableColumnExists('title2', function (TextColumn $column) {
            return $column->isSortable() &&
                $column->isSearchable() &&
                $column->getPrefix() == 'published';
        }, $publishedPost);

    $unpublishedPost = Post::factory()->create([
        'is_published' => false,
    ]);

    livewire(PostsTable::class)
        ->assertTableColumnExists('title2', function (TextColumn $column) {
            return $column->getPrefix() == 'unpublished';
        }, $unpublishedPost);

    $this->expectException('PHPUnit\Framework\ExpectationFailedException');
    $this->expectExceptionMessage('Failed asserting that a column with the name [title] and provided configuration exists on the [' . PostsTable::class . '] component');

    livewire(PostsTable::class)
        ->assertTableColumnExists('title', function (TextColumn $column) {
            return $column->isTime();
        }, $publishedPost);
});

it('can automatically detect boolean cast attribute in icon column', function (): void {
    $post = Post::factory()
        ->create(['is_published' => false]);

    livewire(PostsTable::class)
        ->assertTableColumnExists('is_published', function (IconColumn $column) {
            return $column->isBoolean();
        }, $post);
});
