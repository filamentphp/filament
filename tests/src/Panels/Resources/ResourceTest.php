<?php

use Filament\Facades\Filament;
use Filament\Tests\Models\Post;
use Filament\Tests\Panels\Fixtures\Resources\PostCategoryResource;
use Filament\Tests\Panels\Fixtures\Resources\PostResource;
use Filament\Tests\Panels\Fixtures\Resources\Shop\OrderInvoiceResource;
use Filament\Tests\Panels\Fixtures\Resources\Shop\OrderResource;
use Filament\Tests\Panels\Resources\TestCase;
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

it('can generate a slug based on the multi-word model name', function () {
    expect(PostCategoryResource::getSlug())
        ->toBe('post-categories');
});

it('can generate a nested slug based on the model name', function () {
    expect(OrderResource::getSlug())
        ->toBe('shop/orders');
});

it('can generate a nested slug based on the multi-word model name', function () {
    expect(OrderInvoiceResource::getSlug())
        ->toBe('shop/order-invoices');
});

it('can generate a label based on the model name', function () {
    expect(PostResource::getModelLabel())
        ->toBe('post');
});

it('can generate a label based on the multi-word model name', function () {
    expect(PostCategoryResource::getModelLabel())
        ->toBe('post category');
});

it('can generate a plural label based on the model name and locale', function () {
    $originalLocale = app()->getLocale();

    app()->setLocale('en');
    expect(PostResource::getPluralModelLabel())
        ->toBe('posts');

    app()->setLocale('id');
    expect(PostResource::getPluralModelLabel())
        ->toBe('post');

    app()->setLocale($originalLocale);
});

it('can generate a plural label based on the multi-word model name and locale', function () {
    $originalLocale = app()->getLocale();

    app()->setLocale('en');
    expect(PostCategoryResource::getPluralModelLabel())
        ->toBe('post categories');

    app()->setLocale('id');
    expect(PostCategoryResource::getPluralModelLabel())
        ->toBe('post category');

    app()->setLocale($originalLocale);
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

it('can retrieve a page\'s URL from its model', function () {
    $post = Post::factory()->create();

    expect(Filament::getResourceUrl($post, 'edit'))
        ->toEndWith("/posts/{$post->getKey()}/edit");
    expect(Filament::getResourceUrl($post, 'view'))
        ->toEndWith("/posts/{$post->getKey()}");
    expect(Filament::getResourceUrl(Post::class, 'view', ['record' => $post]))
        ->toEndWith("/posts/{$post->getKey()}");
    expect(Filament::getResourceUrl(Post::class))
        ->toEndWith('/posts');
    expect(Filament::getResourceUrl($post))
        ->toEndWith('/posts');
});
