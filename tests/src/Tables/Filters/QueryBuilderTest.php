<?php

use Filament\Tests\Models\Post;
use Filament\Tests\Tables\Fixtures\PostsQueryBuilderTable;
use Filament\Tests\Tables\TestCase;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can filter text constraint by `contains`', function () {
    $posts = Post::factory(10)->create();
    $content = $posts->random()->content;
    $start = fake()->numberBetween(0, strlen($content));
    $filter = substr($content, $start, fake()->numberBetween($start, strlen($content)));

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($posts)
        ->queryBuilderTable('content', 'contains', $filter)
        ->assertCanSeeTableRecords(Post::where('content', 'like', '%' . $filter . '%')->get())
        ->assertCanNotSeeTableRecords(Post::where('content', 'not like', '%' . $filter . '%')->get());
});

it('can filter text constraint by `startsWith`', function () {
    $posts = Post::factory(10)->create();
    $content = $posts->random()->content;
    $filter = substr($content, 0, fake()->numberBetween(1, strlen($content)));

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($posts)
        ->queryBuilderTable('content', 'startsWith', $filter)
        ->assertCanSeeTableRecords(Post::where('content', 'like', $filter . '%')->get())
        ->assertCanNotSeeTableRecords(Post::where('content', 'not like', $filter . '%')->get());
});

it('can filter text constraint by `endsWith`', function () {
    $posts = Post::factory(10)->create();
    $content = $posts->random()->content;
    $filter = substr($content, -(fake()->numberBetween(1, strlen($content))));

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($posts)
        ->queryBuilderTable('content', 'endsWith', $filter)
        ->assertCanSeeTableRecords(Post::where('content', 'like', '%' . $filter)->get())
        ->assertCanNotSeeTableRecords(Post::where('content', 'not like', '%' . $filter)->get());
});

it('can filter text constraint by `equals`', function () {
    $posts = Post::factory(10)->create();
    $content = $posts->random()->content;

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($posts)
        ->queryBuilderTable('content', 'equals', $content)
        ->assertCanSeeTableRecords(Post::where('content', $content)->get())
        ->assertCanNotSeeTableRecords(Post::where('content', '<>', $content)->get());
});

it('can filter text constraint by `isFilled`', function () {
    Post::factory()->create();
    Post::factory()->create(['content' => null]);
    Post::factory()->create(['content' => '']);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords(Post::all())
        ->queryBuilderTable('content', 'isFilled')
        ->assertCanSeeTableRecords(Post::where('content', '<>', null)->where('content', '<>', '')->get())
        ->assertCanNotSeeTableRecords(Post::where('content', null)->orWhere('content', '')->get());
});
