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
    $this->get(PostResource::getUrl('edit', [
        'record' => Post::factory()->create(),
    ]))->assertSuccessful();
});

it('can retrieve data', function () {
    $post = Post::factory()->create();

    livewire(PostResource\Pages\EditPost::class, [
        'record' => $post->getKey(),
    ])
        ->assertSet('data.author_id', $post->author->getKey())
        ->assertSet('data.content', $post->content)
        ->assertSet('data.tags', $post->tags)
        ->assertSet('data.title', $post->title);
});

it('can save', function () {
    $post = Post::factory()->create();
    $newData = Post::factory()->make();

    livewire(PostResource\Pages\EditPost::class, [
        'record' => $post->getKey(),
    ])
        ->set('data.author_id', $newData->author->getKey())
        ->set('data.content', $newData->content)
        ->set('data.tags', $newData->tags)
        ->set('data.title', $newData->title)
        ->call('save');

    expect($post->refresh())
        ->author->toBeSameModel($newData->author)
        ->content->toBe($newData->content)
        ->tags->toBe($newData->tags)
        ->title->toBe($newData->title);
});

it('can validate input', function () {
    $post = Post::factory()->create();
    $newData = Post::factory()->make();

    livewire(PostResource\Pages\EditPost::class, [
        'record' => $post->getKey(),
    ])
        ->set('data.title', null)
        ->call('save')
        ->assertHasErrors(['data.title' => 'required']);
});
