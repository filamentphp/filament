<?php

use Filament\Tests\Models\Post;
use Filament\Tests\Panels\Fixtures\Resources\PostResource;
use Filament\Tests\Panels\Fixtures\Resources\PostResource\Pages\CreateAnotherPreservingDataPost;
use Filament\Tests\Panels\Fixtures\Resources\PostResource\Pages\CreatePost;
use Filament\Tests\Panels\Resources\TestCase;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can render page', function () {
    $this->get(PostResource::getUrl('create'))
        ->assertSuccessful();
});

it('can create', function () {
    $newData = Post::factory()->make();

    livewire(CreatePost::class)
        ->fillForm([
            'author_id' => $newData->author->getKey(),
            'content' => $newData->content,
            'tags' => $newData->tags,
            'title' => $newData->title,
            'rating' => $newData->rating,
        ])
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertRedirect();

    $this->assertDatabaseHas(Post::class, [
        'author_id' => $newData->author->getKey(),
        'content' => $newData->content,
        'tags' => json_encode($newData->tags),
        'title' => $newData->title,
        'rating' => $newData->rating,
    ]);
});

it('can create another', function () {
    $newData = Post::factory()->make();
    $newData2 = Post::factory()->make();

    livewire(CreatePost::class)
        ->fillForm([
            'author_id' => $newData->author->getKey(),
            'content' => $newData->content,
            'tags' => $newData->tags,
            'title' => $newData->title,
            'rating' => $newData->rating,
        ])
        ->call('create', true)
        ->assertHasNoFormErrors()
        ->assertNoRedirect()
        ->assertFormSet([
            'author_id' => null,
            'content' => null,
            'tags' => [],
            'title' => null,
            'rating' => null,
        ])
        ->fillForm([
            'author_id' => $newData2->author->getKey(),
            'content' => $newData2->content,
            'tags' => $newData2->tags,
            'title' => $newData2->title,
            'rating' => $newData2->rating,
        ])
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertRedirect();

    $this->assertDatabaseHas(Post::class, [
        'author_id' => $newData->author->getKey(),
        'content' => $newData->content,
        'tags' => json_encode($newData->tags),
        'title' => $newData->title,
        'rating' => $newData->rating,
    ]);

    $this->assertDatabaseHas(Post::class, [
        'author_id' => $newData2->author->getKey(),
        'content' => $newData2->content,
        'tags' => json_encode($newData2->tags),
        'title' => $newData2->title,
        'rating' => $newData2->rating,
    ]);
});

it('can create another and preserve data', function () {
    $newData = Post::factory()->make();
    $newData2 = Post::factory()->make();

    livewire(CreateAnotherPreservingDataPost::class)
        ->fillForm([
            'author_id' => $newData->author->getKey(),
            'content' => $newData->content,
            'tags' => $newData->tags,
            'title' => $newData->title,
            'rating' => $newData->rating,
        ])
        ->call('create', true)
        ->assertHasNoFormErrors()
        ->assertNoRedirect()
        ->assertFormSet([
            'author_id' => null,
            'content' => null,
            'tags' => $newData->tags,
            'title' => null,
            'rating' => $newData->rating,
        ])
        ->fillForm([
            'author_id' => $newData2->author->getKey(),
            'content' => $newData2->content,
            'title' => $newData2->title,
        ])
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertRedirect();

    $this->assertDatabaseHas(Post::class, [
        'author_id' => $newData->author->getKey(),
        'content' => $newData->content,
        'tags' => json_encode($newData->tags),
        'title' => $newData->title,
        'rating' => $newData->rating,
    ]);

    $this->assertDatabaseHas(Post::class, [
        'author_id' => $newData2->author->getKey(),
        'content' => $newData2->content,
        'tags' => json_encode($newData->tags),
        'title' => $newData2->title,
        'rating' => $newData->rating,
    ]);
});

it('can validate input', function () {
    Post::factory()->make();

    livewire(CreatePost::class)
        ->fillForm([
            'title' => null,
        ])
        ->call('create')
        ->assertHasFormErrors(['title' => 'required']);
});
