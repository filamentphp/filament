<?php

use Filament\Facades\Filament;
use Filament\Pages\Actions\DeleteAction;
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
        ->assertFormSet([
            'author_id' => $post->author->getKey(),
            'content' => $post->content,
            'tags' => $post->tags,
            'title' => $post->title,
            'rating' => $post->rating,
        ]);
});

it('can save', function () {
    $post = Post::factory()->create();
    $newData = Post::factory()->make();

    livewire(PostResource\Pages\EditPost::class, [
        'record' => $post->getKey(),
    ])
        ->fillForm([
            'author_id' => $newData->author->getKey(),
            'content' => $newData->content,
            'tags' => $newData->tags,
            'title' => $newData->title,
            'rating' => $newData->rating,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($post->refresh())
        ->author->toBeSameModel($newData->author)
        ->content->toBe($newData->content)
        ->tags->toBe($newData->tags)
        ->title->toBe($newData->title);
});

it('can validate input', function () {
    $post = Post::factory()->create();

    livewire(PostResource\Pages\EditPost::class, [
        'record' => $post->getKey(),
    ])
        ->fillForm([
            'title' => null,
        ])
        ->call('save')
        ->assertHasFormErrors(['title' => 'required']);
});

it('can delete', function () {
    $post = Post::factory()->create();

    livewire(PostResource\Pages\EditPost::class, [
        'record' => $post->getKey(),
    ])
        ->callPageAction(DeleteAction::class);

    $this->assertModelMissing($post);
});
