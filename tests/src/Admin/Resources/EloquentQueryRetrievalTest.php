<?php

use Filament\Tests\Admin\Fixtures\PostResource;
use Filament\Tests\Admin\Resources\TestCase;
use Filament\Tests\Models\Post;
use Illuminate\Database\Eloquent\Builder;

uses(TestCase::class);

it('can retrieve Eloquent query for model', function () {
    expect(PostResource::getEloquentQuery())
        ->toBeInstanceOf(Builder::class)
        ->getModel()->toBeInstanceOf(Post::class);
});
