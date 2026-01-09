<?php

use Filament\Actions\DissociateBulkAction;
use Filament\Actions\Testing\TestAction;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Fixtures\Resources\Users\Pages\EditUser;
use Filament\Tests\Fixtures\Resources\Users\RelationManagers\PostsWithDissociateBulkActionRelationManager;
use Filament\Tests\Panels\Resources\TestCase;

use function Filament\Tests\livewire;
use function Pest\Laravel\assertDatabaseHas;

uses(TestCase::class);

it('can render `DissociateBulkAction`', function (): void {
    $user = User::factory()->create();

    livewire(PostsWithDissociateBulkActionRelationManager::class, ['ownerRecord' => $user, 'pageClass' => EditUser::class])
        ->assertActionExists(TestAction::make(DissociateBulkAction::class)->table()->bulk());
});

it('can mount `DissociateBulkAction` confirmation modal', function (): void {
    $user = User::factory()->create();
    $posts = Post::factory()->count(3)->create(['author_id' => $user->id]);

    livewire(PostsWithDissociateBulkActionRelationManager::class, ['ownerRecord' => $user, 'pageClass' => EditUser::class])
        ->selectTableRecords($posts)
        ->mountAction(TestAction::make(DissociateBulkAction::class)->table()->bulk())
        ->assertActionMounted(TestAction::make(DissociateBulkAction::class)->table()->bulk());
});

it('can dissociate selected records using `DissociateBulkAction`', function (): void {
    $user = User::factory()->create();
    $posts = Post::factory()->count(3)->create(['author_id' => $user->id]);

    livewire(PostsWithDissociateBulkActionRelationManager::class, ['ownerRecord' => $user, 'pageClass' => EditUser::class])
        ->callTableBulkAction(DissociateBulkAction::class, $posts);

    foreach ($posts as $post) {
        expect($post->refresh()->author_id)->toBeNull();
    }
});

it('does not delete records when dissociating', function (): void {
    $user = User::factory()->create();
    $posts = Post::factory()->count(3)->create(['author_id' => $user->id]);

    livewire(PostsWithDissociateBulkActionRelationManager::class, ['ownerRecord' => $user, 'pageClass' => EditUser::class])
        ->callTableBulkAction(DissociateBulkAction::class, $posts);

    foreach ($posts as $post) {
        assertDatabaseHas('posts', ['id' => $post->getKey()]);
    }
});

it('can show success notification after dissociating records', function (): void {
    $user = User::factory()->create();
    $posts = Post::factory()->count(2)->create(['author_id' => $user->id]);

    livewire(PostsWithDissociateBulkActionRelationManager::class, ['ownerRecord' => $user, 'pageClass' => EditUser::class])
        ->callTableBulkAction(DissociateBulkAction::class, $posts)
        ->assertNotified();
});

it('only dissociates selected records', function (): void {
    $user = User::factory()->create();
    $selectedPosts = Post::factory()->count(2)->create(['author_id' => $user->id]);
    $unselectedPosts = Post::factory()->count(2)->create(['author_id' => $user->id]);

    livewire(PostsWithDissociateBulkActionRelationManager::class, ['ownerRecord' => $user, 'pageClass' => EditUser::class])
        ->callTableBulkAction(DissociateBulkAction::class, $selectedPosts);

    foreach ($selectedPosts as $post) {
        expect($post->refresh()->author_id)->toBeNull();
    }

    foreach ($unselectedPosts as $post) {
        expect($post->refresh()->author_id)->toBe($user->id);
    }
});
