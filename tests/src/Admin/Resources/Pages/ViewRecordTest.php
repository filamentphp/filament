<?php

use Filament\Facades\Filament;
use Filament\Tests\Admin\Fixtures\Resources\PostResource;
use Filament\Tests\Admin\Resources\TestCase;
use Filament\Tests\Models\Post;
use function Pest\Livewire\livewire;

uses(TestCase::class);

beforeEach(function () {
    Filament::registerResources([
        PostResource::class,
    ]);
});

it('can render page', function () {
    $this->get(PostResource::getUrl('view', [
        'record' => Post::factory()->create(),
    ]))->assertSuccessful();
});

it('can retrieve data', function () {
    $post = Post::factory()->create();

    livewire(PostResource\Pages\ViewPost::class, [
        'record' => $post->getKey(),
    ])
        ->assertSet('data.author_id', $post->author->getKey())
        ->assertSet('data.content', $post->content)
        ->assertSet('data.tags', $post->tags)
        ->assertSet('data.title', $post->title);
});
