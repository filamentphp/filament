<?php

use Filament\Actions\DeleteAction;
use Filament\Tests\Models\Post;
use Filament\Tests\Panels\Fixtures\Resources\PostResource;
use Filament\Tests\Panels\Fixtures\Resources\PostResource\Pages\EditPost;
use Filament\Tests\Panels\Resources\TestCase;
use Illuminate\Support\Str;

use function Filament\Tests\livewire;
use function Pest\Laravel\assertSoftDeleted;

uses(TestCase::class);

it('can render page', function () {
    $this->get(PostResource::getUrl('edit', [
        'record' => Post::factory()->create(),
    ]))->assertSuccessful();
});

it('can retrieve data', function () {
    $post = Post::factory()->create();

    livewire(EditPost::class, [
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

    livewire(EditPost::class, [
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

    livewire(EditPost::class, [
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

    livewire(EditPost::class, [
        'record' => $post->getKey(),
    ])
        ->callAction(DeleteAction::class);

    assertSoftDeleted($post);
});

it('can refresh data', function () {
    $post = Post::factory()->create();

    $page = livewire(EditPost::class, [
        'record' => $post->getKey(),
    ]);

    $originalPostTitle = $post->title;

    $page->assertFormSet([
        'title' => $originalPostTitle,
    ]);

    $newPostTitle = Str::random();

    $post->title = $newPostTitle;
    $post->save();

    $page->assertFormSet([
        'title' => $originalPostTitle,
    ]);

    $page->call('refreshTitle');

    $page->assertFormSet([
        'title' => $newPostTitle,
    ]);
});
