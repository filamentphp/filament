<?php

use Filament\Tests\Admin\Fixtures\Resources\PostResource;
use Filament\Tests\Admin\Resources\TestCase;
use Filament\Tests\Models\Post;
use Illuminate\Database\Eloquent\Builder;

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
    expect(PostResource::getLabel())
        ->toBe('post');
});

it('can generate a plural label based on the model name', function () {
    expect(PostResource::getPluralLabel())
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
