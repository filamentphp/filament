<?php

use Filament\Tests\Admin\Fixtures\Resources\PostResource;
use Filament\Tests\Admin\Resources\TestCase;
use Filament\Tests\Models\Post;
use function Pest\Livewire\livewire;

uses(TestCase::class);

it('can render page', function () {
    $this->get(PostResource::getUrl('create'))->assertSuccessful();
});

it('can create', function () {
    $newData = Post::factory()->make();

    livewire(PostResource\Pages\CreatePost::class)
        ->set('data.author_id', $newData->author->getKey())
        ->set('data.content', $newData->content)
        ->set('data.tags', $newData->tags)
        ->set('data.title', $newData->title)
        ->call('create');

    $this->assertDatabaseHas(Post::class, [
        'author_id' => $newData->author->getKey(),
        'content' => $newData->content,
        'tags' => json_encode($newData->tags),
        'title' => $newData->title,
    ]);
});

it('can validate input', function () {
    $newData = Post::factory()->make();

    livewire(PostResource\Pages\CreatePost::class)
        ->set('data.title', null)
        ->call('create')
        ->assertHasErrors(['data.title' => 'required']);
});
