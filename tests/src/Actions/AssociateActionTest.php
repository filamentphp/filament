<?php

use Filament\Actions\AssociateAction;
use Filament\Actions\Testing\TestAction;
use Filament\Forms\Components\Select;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Fixtures\Resources\Users\Pages\EditUser;
use Filament\Tests\Fixtures\Resources\Users\RelationManagers\PostsWithAssociateActionRelationManager;
use Filament\Tests\Fixtures\Resources\Users\RelationManagers\PostsWithModifiedAssociateQueryRelationManager;
use Filament\Tests\Fixtures\Resources\Users\RelationManagers\PostsWithMultipleModifiedAssociateQueryRelationManager;
use Filament\Tests\Fixtures\Resources\Users\RelationManagers\PostsWithPreloadedAssociateRelationManager;
use Filament\Tests\Panels\Resources\TestCase;

use function Filament\Tests\livewire;

uses(TestCase::class);

describe('associating records', function (): void {
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
});

describe('record select options', function (): void {
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

    it('rejects a `recordId` excluded by `recordSelectOptionsQuery()` when submitted directly', function (): void {
        $user = User::factory()->create();
        Post::factory()->create(['title' => 'Published Article', 'author_id' => null]);
        $outOfScopePost = Post::factory()->create(['title' => 'Draft Post', 'author_id' => null]);

        livewire(PostsWithModifiedAssociateQueryRelationManager::class, ['ownerRecord' => $user, 'pageClass' => EditUser::class])
            ->callAction(TestAction::make(AssociateAction::class)->table(), [
                'recordId' => $outOfScopePost->getKey(),
            ])
            ->assertHasActionErrors(['recordId']);

        expect($outOfScopePost->refresh()->author_id)->toBeNull();
    });

    it('rejects a multi-associate batch containing an out-of-scope `recordId`', function (): void {
        $user = User::factory()->create();
        $inScopePost = Post::factory()->create(['title' => 'Published Article', 'author_id' => null]);
        $outOfScopePost = Post::factory()->create(['title' => 'Draft Post', 'author_id' => null]);

        livewire(PostsWithMultipleModifiedAssociateQueryRelationManager::class, ['ownerRecord' => $user, 'pageClass' => EditUser::class])
            ->callAction(TestAction::make(AssociateAction::class)->table(), [
                'recordId' => [$inScopePost->getKey(), $outOfScopePost->getKey()],
            ])
            ->assertHasActionErrors();

        expect($outOfScopePost->refresh()->author_id)->toBeNull();
        expect($inScopePost->refresh()->author_id)->toBeNull();
    });

    it('applies `recordSelectOptionsQuery()` to search results', function (): void {
        $user = User::factory()->create();
        Post::factory()->create(['title' => 'Published Article', 'author_id' => null]);
        Post::factory()->create(['title' => 'Draft Article', 'author_id' => null]);

        livewire(PostsWithModifiedAssociateQueryRelationManager::class, ['ownerRecord' => $user, 'pageClass' => EditUser::class])
            ->mountAction(TestAction::make(AssociateAction::class)->table())
            ->assertSchemaComponentExists('recordId', checkComponentUsing: function (Select $select): bool {
                $results = $select->getSearchResults('Article');

                expect($results)->toHaveCount(1);
                expect(array_values($results))->toContain('Published Article');
                expect(array_values($results))->not->toContain('Draft Article');

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
});

describe('option labels', function (): void {
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

    it('returns `null` from `getOptionLabel()` when `recordSelectOptionsQuery()` excludes the record', function (): void {
        $user = User::factory()->create();
        $outOfScopePost = Post::factory()->create(['title' => 'Draft Post', 'author_id' => null]);

        livewire(PostsWithModifiedAssociateQueryRelationManager::class, ['ownerRecord' => $user, 'pageClass' => EditUser::class])
            ->mountAction(TestAction::make(AssociateAction::class)->table())
            ->fillForm(['recordId' => $outOfScopePost->getKey()])
            ->assertSchemaComponentExists('recordId', checkComponentUsing: function (Select $select): bool {
                expect($select->getOptionLabel(withDefault: false))->toBeNull();

                return true;
            });
    });

    it('omits out-of-scope records from `getOptionLabels()` when `recordSelectOptionsQuery()` excludes them', function (): void {
        $user = User::factory()->create();
        $inScopePost = Post::factory()->create(['title' => 'Published Article', 'author_id' => null]);
        $outOfScopePost = Post::factory()->create(['title' => 'Draft Post', 'author_id' => null]);

        livewire(PostsWithMultipleModifiedAssociateQueryRelationManager::class, ['ownerRecord' => $user, 'pageClass' => EditUser::class])
            ->mountAction(TestAction::make(AssociateAction::class)->table())
            ->fillForm(['recordId' => [$inScopePost->getKey(), $outOfScopePost->getKey()]])
            ->assertSchemaComponentExists('recordId', checkComponentUsing: function (Select $select) use ($inScopePost, $outOfScopePost): bool {
                $labels = $select->getOptionLabels(withDefaults: false);

                expect($labels)->toHaveCount(1);
                expect($labels)->toHaveKey($inScopePost->getKey());
                expect($labels)->not->toHaveKey($outOfScopePost->getKey());

                return true;
            });
    });
});

it('can set `associateAnother()`', function (): void {
    $action = AssociateAction::make();

    expect($action->canAssociateAnother())->toBeTrue();

    $action->associateAnother(false);

    expect($action->canAssociateAnother())->toBeFalse();
});

it('can set `recordSelectSearchColumns()`', function (): void {
    $action = AssociateAction::make()
        ->recordSelectSearchColumns(['name', 'email']);

    expect($action->getRecordSelectSearchColumns())->toBe(['name', 'email']);
});

it('can set `forceSearchCaseInsensitive()`', function (): void {
    $action = AssociateAction::make();

    expect($action->isSearchForcedCaseInsensitive())->toBeNull();

    $action->forceSearchCaseInsensitive();

    expect($action->isSearchForcedCaseInsensitive())->toBeTrue();
});

it('can set `multiple()`', function (): void {
    $action = AssociateAction::make();

    expect($action->isMultiple())->toBeFalse();

    $action->multiple();

    expect($action->isMultiple())->toBeTrue();
});

it('has `associate` as default name', function (): void {
    expect(AssociateAction::getDefaultName())->toBe('associate');
});

it('can set `associateAnother()` with a `Closure`', function (): void {
    $action = AssociateAction::make()
        ->associateAnother(static fn (): bool => false);

    expect($action->canAssociateAnother())->toBeFalse();
});

it('can disable `associateAnother()` via deprecated `disableAssociateAnother()`', function (): void {
    $action = AssociateAction::make();

    expect($action->canAssociateAnother())->toBeTrue();

    $action->disableAssociateAnother();

    expect($action->canAssociateAnother())->toBeFalse();
});

it('can set `preloadRecordSelect()`', function (): void {
    $action = AssociateAction::make();

    expect($action->isRecordSelectPreloaded())->toBeFalse();

    $action->preloadRecordSelect();

    expect($action->isRecordSelectPreloaded())->toBeTrue();
});

it('can set `recordSelectSearchColumns()` with a `Closure`', function (): void {
    $action = AssociateAction::make()
        ->recordSelectSearchColumns(static fn (): array => ['title', 'slug']);

    expect($action->getRecordSelectSearchColumns())->toBe(['title', 'slug']);
});

it('can clear `recordSelectSearchColumns()` with `null`', function (): void {
    $action = AssociateAction::make()
        ->recordSelectSearchColumns(['title'])
        ->recordSelectSearchColumns(null);

    expect($action->getRecordSelectSearchColumns())->toBeNull();
});

it('returns fluent `$this` from `recordSelect()`', function (): void {
    $action = AssociateAction::make();

    $result = $action->recordSelect(static fn ($select) => $select);

    expect($result)->toBe($action);
});

it('returns fluent `$this` from `recordSelectOptionsQuery()`', function (): void {
    $action = AssociateAction::make();

    $result = $action->recordSelectOptionsQuery(static fn ($query) => $query);

    expect($result)->toBe($action);
});

it('can set `forceSearchCaseInsensitive()` to `false`', function (): void {
    $action = AssociateAction::make()
        ->forceSearchCaseInsensitive()
        ->forceSearchCaseInsensitive(false);

    expect($action->isSearchForcedCaseInsensitive())->toBeFalse();
});

describe('Closure support', function (): void {
    it('can set `associateAnother()` with a `Closure`', function (): void {
        $action = AssociateAction::make()
            ->associateAnother(static fn (): bool => false);

        expect($action->canAssociateAnother())->toBeFalse();
    });

    it('can set `preloadRecordSelect()` with a `Closure`', function (): void {
        $action = AssociateAction::make()
            ->preloadRecordSelect(static fn (): bool => true);

        expect($action->isRecordSelectPreloaded())->toBeTrue();
    });

    it('can set `multiple()` with a `Closure`', function (): void {
        $action = AssociateAction::make()
            ->multiple(static fn (): bool => true);

        expect($action->isMultiple())->toBeTrue();
    });

    it('can set `forceSearchCaseInsensitive()` with a `Closure`', function (): void {
        $action = AssociateAction::make()
            ->forceSearchCaseInsensitive(static fn (): bool => true);

        expect($action->isSearchForcedCaseInsensitive())->toBeTrue();
    });

    it('can set `recordSelectSearchColumns()` with a `Closure`', function (): void {
        $action = AssociateAction::make()
            ->recordSelectSearchColumns(static fn (): array => ['name', 'email']);

        expect($action->getRecordSelectSearchColumns())->toBe(['name', 'email']);
    });
});

it('returns `associate` from `getDefaultName()`', function (): void {
    expect(AssociateAction::getDefaultName())->toBe('associate');
});

it('returns `Select` component from `getRecordSelect()`', function (): void {
    $action = AssociateAction::make();

    $select = $action->getRecordSelect();

    expect($select)->toBeInstanceOf(Select::class);
});
