<?php

use Filament\Tests\Admin\Fixtures\Resources\PostResource;
use Filament\Tests\Admin\Fixtures\Resources\UserResource;
use Filament\Tests\Admin\Resources\TestCase;
use Filament\Tests\Models\Post;
use function Pest\Livewire\livewire;

uses(TestCase::class);

it('can render posts page', function () {
    $this->get(PostResource::getUrl('index'))->assertSuccessful();
});

it('can render users page', function () {
    $this->get(UserResource::getUrl('index'))->assertSuccessful();
});

it('can list post titles', function () {
    $posts = Post::factory()->count(10)->create();

    livewire(PostResource\Pages\ListPosts::class)
        ->assertSeeInOrder($posts->pluck('title')->toArray());
});

it('can list post authors', function () {
    $posts = Post::factory()->count(10)->create();

    livewire(PostResource\Pages\ListPosts::class)
        ->assertSeeInOrder($posts->pluck('author.name')->toArray());
});

it('can delete posts', function () {
    $post = Post::factory()->create();

    livewire(PostResource\Pages\ListPosts::class)
        ->callTableAction('delete', $post);

    $this->assertDatabaseMissing(Post::class, [
        'id' => $post->id,
    ]);
});

it('can bulk delete posts', function () {
    $posts = Post::factory()->count(10)->create();

    livewire(PostResource\Pages\ListPosts::class)
        ->callTableBulkAction('delete', $posts);

    foreach ($posts as $post) {
        $this->assertDatabaseMissing(Post::class, [
            'id' => $post->id,
        ]);
    }
});
