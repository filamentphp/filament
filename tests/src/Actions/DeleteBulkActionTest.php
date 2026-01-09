<?php

use Filament\Actions\DeleteBulkAction;
use Filament\Actions\Testing\TestAction;
use Filament\Tests\Fixtures\Livewire\PostsTable;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Tables\TestCase;

use function Filament\Tests\livewire;
use function Pest\Laravel\assertSoftDeleted;

uses(TestCase::class);

it('can render `DeleteBulkAction`', function (): void {
    livewire(PostsTable::class)
        ->assertActionExists(TestAction::make(DeleteBulkAction::class)->table()->bulk());
});

it('can mount `DeleteBulkAction` confirmation modal', function (): void {
    $posts = Post::factory()->count(3)->create();

    livewire(PostsTable::class)
        ->selectTableRecords($posts)
        ->mountAction(TestAction::make(DeleteBulkAction::class)->table()->bulk())
        ->assertActionMounted(TestAction::make(DeleteBulkAction::class)->table()->bulk());
});

it('can delete selected records using `DeleteBulkAction`', function (): void {
    $posts = Post::factory()->count(5)->create();

    livewire(PostsTable::class)
        ->callTableBulkAction(DeleteBulkAction::class, $posts);

    foreach ($posts as $post) {
        assertSoftDeleted($post);
    }
});

it('can show success notification after deleting records', function (): void {
    $posts = Post::factory()->count(3)->create();

    livewire(PostsTable::class)
        ->callTableBulkAction(DeleteBulkAction::class, $posts)
        ->assertNotified();
});

it('removes records from table after bulk deletion', function (): void {
    $posts = Post::factory()->count(3)->create();

    livewire(PostsTable::class)
        ->assertCanSeeTableRecords($posts)
        ->callTableBulkAction(DeleteBulkAction::class, $posts)
        ->assertCanNotSeeTableRecords($posts);
});

it('only deletes selected records', function (): void {
    $selectedPosts = Post::factory()->count(2)->create();
    $unselectedPosts = Post::factory()->count(2)->create();

    livewire(PostsTable::class)
        ->callTableBulkAction(DeleteBulkAction::class, $selectedPosts);

    foreach ($selectedPosts as $post) {
        assertSoftDeleted($post);
    }

    foreach ($unselectedPosts as $post) {
        expect($post->refresh()->deleted_at)->toBeNull();
    }
});
