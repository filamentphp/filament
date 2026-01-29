<?php

use Filament\Actions\AssociateAction;
use Filament\Actions\Testing\TestAction;
use Filament\Forms\Components\Select;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Fixtures\Resources\Users\Pages\EditUser;
use Filament\Tests\Fixtures\Resources\Users\RelationManagers\PostsWithAssociateActionRelationManager;
use Filament\Tests\Fixtures\Resources\Users\RelationManagers\PostsWithModifiedAssociateQueryRelationManager;
use Filament\Tests\Fixtures\Resources\Users\RelationManagers\PostsWithPreloadedAssociateRelationManager;
use Filament\Tests\Panels\Resources\TestCase;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can render `AssociateAction`', function (): void {
    $user = User::factory()->create();

    livewire(PostsWithAssociateActionRelationManager::class, ['ownerRecord' => $user, 'pageClass' => EditUser::class])
        ->assertActionExists(TestAction::make(AssociateAction::class)->table());
});

it('can mount `AssociateAction` modal', function (): void {
    $user = User::factory()->create();

    livewire(PostsWithAssociateActionRelationManager::class, ['ownerRecord' => $user, 'pageClass' => EditUser::class])
        ->mountAction(TestAction::make(AssociateAction::class)->table())
        ->assertActionMounted(TestAction::make(AssociateAction::class)->table());
});

it('can associate a record using `AssociateAction`', function (): void {
    $user = User::factory()->create();
    $post = Post::factory()->create(['author_id' => null]);

    livewire(PostsWithAssociateActionRelationManager::class, ['ownerRecord' => $user, 'pageClass' => EditUser::class])
        ->callAction(TestAction::make(AssociateAction::class)->table(), [
            'recordId' => $post->getKey(),
        ])
        ->assertHasNoFormErrors();

    expect($post->refresh()->author_id)->toBe($user->id);
});

it('can associate multiple records using `AssociateAction`', function (): void {
    $user = User::factory()->create();
    $posts = Post::factory()->count(3)->create(['author_id' => null]);

    livewire(PostsWithPreloadedAssociateRelationManager::class, ['ownerRecord' => $user, 'pageClass' => EditUser::class])
        ->callAction(TestAction::make(AssociateAction::class)->table(), [
            'recordId' => $posts->pluck('id')->all(),
        ])
        ->assertHasNoFormErrors();

    foreach ($posts as $post) {
        expect($post->refresh()->author_id)->toBe($user->id);
    }
});

it('can show success notification after associating a record', function (): void {
    $user = User::factory()->create();
    $post = Post::factory()->create(['author_id' => null]);

    livewire(PostsWithAssociateActionRelationManager::class, ['ownerRecord' => $user, 'pageClass' => EditUser::class])
        ->callAction(TestAction::make(AssociateAction::class)->table(), [
            'recordId' => $post->getKey(),
        ])
        ->assertNotified();
});

it('shows associated record in table', function (): void {
    $user = User::factory()->create();
    $post = Post::factory()->create(['author_id' => null]);

    livewire(PostsWithAssociateActionRelationManager::class, ['ownerRecord' => $user, 'pageClass' => EditUser::class])
        ->assertCanNotSeeTableRecords([$post])
        ->callAction(TestAction::make(AssociateAction::class)->table(), [
            'recordId' => $post->getKey(),
        ])
        ->assertCanSeeTableRecords([$post]);
});

it('can get `getOptions()` for record select with preload', function (): void {
    $user = User::factory()->create();
    $posts = Post::factory()->count(3)->create(['author_id' => null]);

    livewire(PostsWithPreloadedAssociateRelationManager::class, ['ownerRecord' => $user, 'pageClass' => EditUser::class])
        ->mountAction(TestAction::make(AssociateAction::class)->table())
        ->assertSchemaComponentExists('recordId', checkComponentUsing: function (Select $select) use ($posts): bool {
            $options = $select->getOptions();

            expect($options)->toHaveCount(3);
            expect(array_values($options))->toContain($posts[0]->title);
            expect(array_values($options))->toContain($posts[1]->title);
            expect(array_values($options))->toContain($posts[2]->title);

            return true;
        });
});

it('returns empty array for `getOptions()` when not preloaded', function (): void {
    $user = User::factory()->create();
    Post::factory()->count(3)->create(['author_id' => null]);

    livewire(PostsWithAssociateActionRelationManager::class, ['ownerRecord' => $user, 'pageClass' => EditUser::class])
        ->mountAction(TestAction::make(AssociateAction::class)->table())
        ->assertSchemaComponentExists('recordId', checkComponentUsing: function (Select $select): bool {
            expect($select->getOptions())->toBe([]);

            return true;
        });
});

it('can get `getSearchResults()` for record select', function (): void {
    $user = User::factory()->create();
    Post::factory()->create(['title' => 'First Article', 'author_id' => null]);
    Post::factory()->create(['title' => 'Second Post', 'author_id' => null]);
    Post::factory()->create(['title' => 'Third Article', 'author_id' => null]);

    livewire(PostsWithAssociateActionRelationManager::class, ['ownerRecord' => $user, 'pageClass' => EditUser::class])
        ->mountAction(TestAction::make(AssociateAction::class)->table())
        ->assertSchemaComponentExists('recordId', checkComponentUsing: function (Select $select): bool {
            $results = $select->getSearchResults('Article');

            expect($results)->toHaveCount(2);
            expect(array_values($results))->toContain('First Article');
            expect(array_values($results))->toContain('Third Article');
            expect(array_values($results))->not->toContain('Second Post');

            return true;
        });
});

it('excludes already associated records from options', function (): void {
    $user = User::factory()->create();
    $associatedPost = Post::factory()->create(['title' => 'Already Associated', 'author_id' => $user->id]);
    $availablePost = Post::factory()->create(['title' => 'Available Post', 'author_id' => null]);

    livewire(PostsWithPreloadedAssociateRelationManager::class, ['ownerRecord' => $user, 'pageClass' => EditUser::class])
        ->mountAction(TestAction::make(AssociateAction::class)->table())
        ->assertSchemaComponentExists('recordId', checkComponentUsing: function (Select $select): bool {
            $options = $select->getOptions();

            expect($options)->toHaveCount(1);
            expect(array_values($options))->toContain('Available Post');
            expect(array_values($options))->not->toContain('Already Associated');

            return true;
        });
});

it('excludes already associated records from search results', function (): void {
    $user = User::factory()->create();
    $associatedPost = Post::factory()->create(['title' => 'Associated Post', 'author_id' => $user->id]);
    $availablePost = Post::factory()->create(['title' => 'Available Post', 'author_id' => null]);

    livewire(PostsWithAssociateActionRelationManager::class, ['ownerRecord' => $user, 'pageClass' => EditUser::class])
        ->mountAction(TestAction::make(AssociateAction::class)->table())
        ->assertSchemaComponentExists('recordId', checkComponentUsing: function (Select $select): bool {
            $results = $select->getSearchResults('Post');

            expect($results)->toHaveCount(1);
            expect(array_values($results))->toContain('Available Post');
            expect(array_values($results))->not->toContain('Associated Post');

            return true;
        });
});

it('can get `getOptionLabel()` for selected record', function (): void {
    $user = User::factory()->create();
    $post = Post::factory()->create(['title' => 'Test Post', 'author_id' => null]);

    livewire(PostsWithAssociateActionRelationManager::class, ['ownerRecord' => $user, 'pageClass' => EditUser::class])
        ->mountAction(TestAction::make(AssociateAction::class)->table())
        ->fillForm(['recordId' => $post->id])
        ->assertSchemaComponentExists('recordId', checkComponentUsing: function (Select $select) use ($post): bool {
            expect($select->getOptionLabel())->toBe($post->title);

            return true;
        });
});

it('can get `getOptionLabels()` for multiple selected records', function (): void {
    $user = User::factory()->create();
    $posts = Post::factory()->count(2)->create(['author_id' => null]);

    livewire(PostsWithPreloadedAssociateRelationManager::class, ['ownerRecord' => $user, 'pageClass' => EditUser::class])
        ->mountAction(TestAction::make(AssociateAction::class)->table())
        ->fillForm(['recordId' => $posts->pluck('id')->all()])
        ->assertSchemaComponentExists('recordId', checkComponentUsing: function (Select $select) use ($posts): bool {
            $labels = $select->getOptionLabels();

            expect($labels)->toHaveCount(2);
            expect(array_values($labels))->toContain($posts[0]->title);
            expect(array_values($labels))->toContain($posts[1]->title);

            return true;
        });
});

it('can use `recordSelectOptionsQuery()` to modify query', function (): void {
    $user = User::factory()->create();
    Post::factory()->create(['title' => 'Published Article', 'author_id' => null]);
    Post::factory()->create(['title' => 'Draft Post', 'author_id' => null]);
    Post::factory()->create(['title' => 'Published Guide', 'author_id' => null]);

    livewire(PostsWithModifiedAssociateQueryRelationManager::class, ['ownerRecord' => $user, 'pageClass' => EditUser::class])
        ->mountAction(TestAction::make(AssociateAction::class)->table())
        ->assertSchemaComponentExists('recordId', checkComponentUsing: function (Select $select): bool {
            $options = $select->getOptions();

            expect($options)->toHaveCount(2);
            expect(array_values($options))->toContain('Published Article');
            expect(array_values($options))->toContain('Published Guide');
            expect(array_values($options))->not->toContain('Draft Post');

            return true;
        });
});

it('respects `optionsLimit()` on record select', function (): void {
    $user = User::factory()->create();
    Post::factory()->count(10)->create(['author_id' => null]);

    livewire(PostsWithPreloadedAssociateRelationManager::class, ['ownerRecord' => $user, 'pageClass' => EditUser::class])
        ->mountAction(TestAction::make(AssociateAction::class)->table())
        ->assertSchemaComponentExists('recordId', checkComponentUsing: function (Select $select): bool {
            // Default options limit is 50
            expect($select->getOptionsLimit())->toBe(50);

            return true;
        });
});
