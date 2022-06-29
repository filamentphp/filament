<?php

use Filament\Tests\Admin\Fixtures\Resources\PostResource;
use Filament\Tests\Admin\Resources\TestCase;
use Filament\Tests\Models\Post;
use Illuminate\Database\Eloquent\Builder;

use function Pest\Livewire\livewire;

uses(TestCase::class);

it('can retrieve Eloquent query for model', function () {
    expect(PostResource::getEloquentQuery())
        ->toBeInstanceOf(Builder::class)
        ->getModel()->toBeInstanceOf(Post::class);
});

it('can generate a slug based on the model name', function () {
    expect(PostResource::getSlug())
        ->toBe('posts');
});

it('can generate a label based on the model name', function () {
    expect(PostResource::getModelLabel())
        ->toBe('post');
});

it('can generate a plural label based on the model name', function () {
    expect(PostResource::getPluralModelLabel())
        ->toBe('posts');
});

it('can retrieve a record\'s title', function () {
    $post = Post::factory()->create();

    expect(PostResource::getRecordTitle($post))
        ->toBe($post->title);
});

it('can resolve record route binding', function () {
    $post = Post::factory()->create();

    expect(PostResource::resolveRecordRouteBinding($post->getKey()))
        ->toBeSameModel($post);
});

it('can retrieve a page\'s URL', function () {
    $post = Post::factory()->create();
    $resourceSlug = PostResource::getSlug();

    expect(PostResource::getUrl('create'))
        ->toContain($resourceSlug)
        ->toContain('create');
    expect(PostResource::getUrl('edit', ['record' => $post]))
        ->toContain($resourceSlug)
        ->toContain(strval($post->getRouteKey()));
    expect(PostResource::getUrl('index'))->toContain($resourceSlug);
    expect(PostResource::getUrl('view', ['record' => $post]))
        ->toContain($resourceSlug)
        ->toContain(strval($post->getRouteKey()));
});

it('can store the filters in the session', function () {
    [$postA, $postB, $postC] = Post::factory(3)
        ->sequence(
            ['rating' => 2],
            ['rating' => 5],
            ['rating' => 10],
        )
        ->create();

    $livewire = livewire(PostResource\Pages\ListPosts::class)
        ->assertSee($postA->title)
        ->assertSee($postB->title)
        ->assertSee($postC->title);

    expect(PostResource::getLastFilteredEloquentQuery()->pluck('id'))
        ->toHaveCount(3)
        ->toContain($postA->id)
        ->toContain($postB->id)
        ->toContain($postC->id);

    $livewire
        ->set('tableFilters.rating.minimum_rating', '5')
        ->assertDontSee($postA->title)
        ->assertSee($postB->title)
        ->assertSee($postC->title);

    expect(PostResource::getLastFilteredEloquentQuery()->pluck('id'))
        ->toHaveCount(2)
        ->not->toContain($postA->id)
        ->toContain($postB->id)
        ->toContain($postC->id);

    expect(session('filament.' . PostResource\Pages\ListPosts::class . '.tableFilters'))
        ->toBe([
            'rating' => ['minimum_rating' => '5'],
        ]);

    // Test that filters are reset on a new page load of the index component.
    $livewire = livewire(PostResource\Pages\ListPosts::class)
        ->assertSee($postA->title)
        ->assertSee($postB->title)
        ->assertSee($postC->title);

    expect(session('filament.' . PostResource\Pages\ListPosts::class . '.tableFilters'))
        ->toBe([]);
});
