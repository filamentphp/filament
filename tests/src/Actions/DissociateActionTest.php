<?php

use Filament\Actions\DissociateAction;
use Filament\Actions\Testing\TestAction;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Fixtures\Resources\Users\Pages\EditUser;
use Filament\Tests\Fixtures\Resources\Users\RelationManagers\PostsWithDissociateActionRelationManager;
use Filament\Tests\Panels\Resources\TestCase;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can render `DissociateAction`', function (): void {
    $user = User::factory()->create();
    $post = Post::factory()->create(['author_id' => $user->id]);

    livewire(PostsWithDissociateActionRelationManager::class, ['ownerRecord' => $user, 'pageClass' => EditUser::class])
        ->assertActionExists(TestAction::make(DissociateAction::class)->table($post));
});

it('can mount `DissociateAction` confirmation modal', function (): void {
    $user = User::factory()->create();
    $post = Post::factory()->create(['author_id' => $user->id]);

    livewire(PostsWithDissociateActionRelationManager::class, ['ownerRecord' => $user, 'pageClass' => EditUser::class])
        ->mountAction(TestAction::make(DissociateAction::class)->table($post))
        ->assertActionMounted(TestAction::make(DissociateAction::class)->table($post));
});

it('can dissociate a record using `DissociateAction`', function (): void {
    $user = User::factory()->create();
    $post = Post::factory()->create(['author_id' => $user->id]);

    livewire(PostsWithDissociateActionRelationManager::class, ['ownerRecord' => $user, 'pageClass' => EditUser::class])
        ->callAction(TestAction::make(DissociateAction::class)->table($post));

    expect($post->refresh()->author_id)->toBeNull();
});

it('does not delete the record when dissociating', function (): void {
    $user = User::factory()->create();
    $post = Post::factory()->create(['author_id' => $user->id]);

    livewire(PostsWithDissociateActionRelationManager::class, ['ownerRecord' => $user, 'pageClass' => EditUser::class])
        ->callAction(TestAction::make(DissociateAction::class)->table($post));

    expect(Post::find($post->getKey()))->not->toBeNull();
});

it('can show success notification after dissociating a record', function (): void {
    $user = User::factory()->create();
    $post = Post::factory()->create(['author_id' => $user->id]);

    livewire(PostsWithDissociateActionRelationManager::class, ['ownerRecord' => $user, 'pageClass' => EditUser::class])
        ->callAction(TestAction::make(DissociateAction::class)->table($post))
        ->assertNotified();
});

it('removes record from table after dissociating', function (): void {
    $user = User::factory()->create();
    $post = Post::factory()->create(['author_id' => $user->id]);

    livewire(PostsWithDissociateActionRelationManager::class, ['ownerRecord' => $user, 'pageClass' => EditUser::class])
        ->assertCanSeeTableRecords([$post])
        ->callAction(TestAction::make(DissociateAction::class)->table($post))
        ->assertCanNotSeeTableRecords([$post]);
});

it('can dissociate multiple records sequentially', function (): void {
    $user = User::factory()->create();
    $post1 = Post::factory()->create(['author_id' => $user->id]);
    $post2 = Post::factory()->create(['author_id' => $user->id]);

    livewire(PostsWithDissociateActionRelationManager::class, ['ownerRecord' => $user, 'pageClass' => EditUser::class])
        ->callAction(TestAction::make(DissociateAction::class)->table($post1))
        ->callAction(TestAction::make(DissociateAction::class)->table($post2));

    expect($post1->refresh()->author_id)->toBeNull();
    expect($post2->refresh()->author_id)->toBeNull();
});
