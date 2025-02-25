<?php

use Filament\Tables\Actions\DeleteAction;
use Filament\Tests\Models\Post;
use Filament\Tests\Tables\Fixtures\PostsQueryBuilderTable;
use Filament\Tests\Tables\TestCase;
use function Filament\Tests\livewire;
use function Pest\Laravel\assertSoftDeleted;

uses(TestCase::class);

it('can filter text constraint for `contains`', function () {
    $posts = Post::factory(10)->create();
    $content = $posts->random()->content;
    $filter = substr($content, 2, 7);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($posts)
        ->queryBuilderTable('content', 'contains', $filter)
        ->assertCanSeeTableRecords(Post::where('content', 'like', '%' . $filter . '%')->get())
        ->assertCanNotSeeTableRecords(Post::where('content', 'not like', '%' . $filter . '%')->get());
});

it('can filter text constraint for `not contains`', function () {
    $posts = Post::factory(10)->create();
    $content = $posts->random()->content;
    $filter = substr($content, 2, 7);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($posts)
        ->queryBuilderTable('content', 'contains.inverse', $filter)
        ->assertCanSeeTableRecords(Post::where('content', 'not like', '%' . $filter . '%')->get())
        ->assertCanNotSeeTableRecords(Post::where('content', 'like', '%' . $filter . '%')->get());
});

it('can filter text constraint for `startsWith`', function () {
    $posts = Post::factory(10)->create();
    $content = $posts->random()->content;
    $filter = substr($content, 0, 5);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($posts)
        ->queryBuilderTable('content', 'startsWith', $filter)
        ->assertCanSeeTableRecords(Post::where('content', 'like', $filter . '%')->get())
        ->assertCanNotSeeTableRecords(Post::where('content', 'not like', $filter . '%')->get());
});

it('can filter text constraint for `not startsWith`', function () {
    $posts = Post::factory(10)->create();
    $content = $posts->random()->content;
    $filter = substr($content, 0, 5);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($posts)
        ->queryBuilderTable('content', 'startsWith.inverse', $filter)
        ->assertCanSeeTableRecords(Post::where('content', 'not like', $filter . '%')->get())
        ->assertCanNotSeeTableRecords(Post::where('content', 'like', $filter . '%')->get());
});

it('can filter text constraint for `endsWith`', function () {
    $posts = Post::factory(10)->create();
    $content = $posts->random()->content;
    $filter = substr($content, -5);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($posts)
        ->queryBuilderTable('content', 'endsWith', $filter)
        ->assertCanSeeTableRecords(Post::where('content', 'like', '%' . $filter)->get())
        ->assertCanNotSeeTableRecords(Post::where('content', 'not like', '%' . $filter)->get());
});

it('can filter text constraint for `not endsWith`', function () {
    $posts = Post::factory(10)->create();
    $content = $posts->random()->content;
    $filter = substr($content, -5);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($posts)
        ->queryBuilderTable('content', 'endsWith.inverse', $filter)
        ->assertCanSeeTableRecords(Post::where('content', 'not like', '%' . $filter)->get())
        ->assertCanNotSeeTableRecords(Post::where('content', 'like', '%' . $filter)->get());
});

it('can filter text constraint for `equals`', function () {
    $posts = Post::factory(10)->create();
    $content = $posts->random()->content;

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($posts)
        ->queryBuilderTable('content', 'equals', $content)
        ->assertCanSeeTableRecords(Post::where('content', $content)->get())
        ->assertCanNotSeeTableRecords(Post::where('content', '<>', $content)->get());
});

it('can filter text constraint for `not equals`', function () {
    $posts = Post::factory(10)->create();
    $content = $posts->random()->content;

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords($posts)
        ->queryBuilderTable('content', 'equals.inverse', $content)
        ->assertCanSeeTableRecords(Post::where('content', '<>', $content)->get())
        ->assertCanNotSeeTableRecords(Post::where('content', $content)->get());
});

it('can filter text constraint for `isFilled`', function () {
    Post::factory(8)->create();
    Post::factory()->create(['content' => null]);
    Post::factory()->create(['content' => '']);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords(Post::all())
        ->queryBuilderTable('content', 'isFilled')
        ->assertCanSeeTableRecords(Post::where('content', '<>', null)->where('content', '<>', '')->get())
        ->assertCanNotSeeTableRecords(Post::where('content', null)->orWhere('content', '')->get());
});

it('can filter text constraint for `not isFilled`', function () {
    Post::factory(8)->create();
    Post::factory()->create(['content' => null]);
    Post::factory()->create(['content' => '']);

    livewire(PostsQueryBuilderTable::class)
        ->assertCanSeeTableRecords(Post::all())
        ->queryBuilderTable('content', 'isFilled.inverse')
        ->assertCanSeeTableRecords(Post::where('content', null)->orWhere('content', '')->get())
        ->assertCanNotSeeTableRecords(Post::where('content', '<>', null)->where('content', '<>', '')->get());
});

// it('can filter records by text constraint in the query builder with modal action', function () {
//     $posts = Post::factory()->count(10)->create();
//     $content = $posts->first()->content;
//     $post = Post::where('content', $content);

//     livewire(PostsQueryBuilderTable::class)
//         ->assertCanSeeTableRecords($posts)
//         ->queryBuilderTable('content', 'contains', $content)
//         ->assertCanSeeTableRecords($post->get())
//         ->callTableAction(DeleteAction::class, $post->first());

//     assertSoftDeleted($post->first());
// });
