<?php

use Filament\Facades\Filament;
use Filament\Tests\Admin\Fixtures\PostResource;
use Filament\Tests\Admin\Resources\TestCase;
use Filament\Tests\Models\Post;

uses(TestCase::class);

beforeEach(function () {
    Filament::registerResources([
        PostResource::class,
    ]);
});

it('can render page', function () {
    $this->get(PostResource::getUrl('edit', [
        'record' => Post::factory()->create(),
    ]))->assertSuccessful();
});
