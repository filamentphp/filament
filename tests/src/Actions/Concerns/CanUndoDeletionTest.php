<?php

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\Testing\TestAction;
use Filament\Notifications\Livewire\Notifications;
use Filament\Notifications\Notification;
use Filament\Notifications\NotificationMailbox;
use Filament\Tests\Fixtures\Livewire\UndoableDeletePostsTable;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Tables\TestCase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

use function Filament\Tests\livewire;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertNotSoftDeleted;
use function Pest\Laravel\assertSoftDeleted;

uses(TestCase::class);

beforeEach(function (): void {
    config(['cache.default' => 'array']);

    UndoableDeletePostsTable::$canRestore = true;
});

/**
 * @return Collection<int, Notification>
 */
function pullUndoDeletionTestNotifications(): Collection
{
    app(NotificationMailbox::class)->flush();

    $component = new Notifications;
    $component->mount();
    $component->pullNotifications();

    return collect($component->notifications);
}

function findUndoDeletionTestAction(Collection $notifications): ?Action
{
    foreach ($notifications as $notification) {
        foreach ($notification->getActions() as $action) {
            if (($action instanceof Action) && ($action->getName() === 'undo')) {
                return $action;
            }
        }
    }

    return null;
}

function getUndoDeletionTestToken(): string
{
    $undoAction = findUndoDeletionTestAction(pullUndoDeletionTestNotifications());

    expect($undoAction)->not->toBeNull();

    return $undoAction->getEventData()['token'];
}

describe('configuration', function (): void {
    it('is not undoable by default', function (): void {
        expect(DeleteAction::make()->isUndoable())->toBeFalse()
            ->and(DeleteBulkAction::make()->isUndoable())->toBeFalse();
    });

    it('can be made undoable', function (): void {
        expect(DeleteAction::make()->undoable()->isUndoable())->toBeTrue()
            ->and(DeleteBulkAction::make()->undoable()->isUndoable())->toBeTrue();
    });

    it('can disable undo with a `false` condition', function (): void {
        expect(DeleteAction::make()->undoable(false)->isUndoable())->toBeFalse();
    });

    it('accepts closures for the condition and seconds', function (): void {
        $action = DeleteAction::make()
            ->undoable(fn (): bool => true, fn (): int => 45);

        expect($action->isUndoable())->toBeTrue()
            ->and($action->getUndoableSeconds())->toBe(45);
    });

    it('defaults to 10 undoable seconds', function (): void {
        expect(DeleteAction::make()->undoable()->getUndoableSeconds())->toBe(10);
    });

    it('can customize the undoable seconds', function (): void {
        expect(DeleteBulkAction::make()->undoable(seconds: 30)->getUndoableSeconds())->toBe(30);
    });

    it('returns fluent `$this` from `undoable()`', function (): void {
        $action = DeleteAction::make();

        expect($action->undoable())->toBe($action);
    });

    it('generates a namespaced cache key from the token', function (): void {
        expect(DeleteAction::getUndoDeletionCacheKey('abc'))->toBe('filament-actions::undo-deletion.abc')
            ->and(DeleteBulkAction::getUndoDeletionCacheKey('abc'))->toBe('filament-actions::undo-deletion.abc');
    });
});

describe('`DeleteAction`', function (): void {
    it('adds an undo action to the success notification', function (): void {
        $post = Post::factory()->create();

        $component = livewire(UndoableDeletePostsTable::class)
            ->callAction(TestAction::make('delete')->table($post));

        assertSoftDeleted($post);

        $notifications = pullUndoDeletionTestNotifications();
        $undoAction = findUndoDeletionTestAction($notifications);

        expect($undoAction)->not->toBeNull()
            ->and($undoAction->getLabel())->toBe('Undo')
            ->and($undoAction->getEvent())->toBe('filament-actions::undo-deletion')
            ->and($undoAction->getEventData()['id'])->toBe($component->id())
            ->and($undoAction->getEventData()['token'])->toMatch('/\A[a-zA-Z0-9]{40}\z/')
            ->and($notifications->first()->getDuration())->toBe(10000);
    });

    it('stores the deleted record keys in the cache', function (): void {
        $post = Post::factory()->create();

        $component = livewire(UndoableDeletePostsTable::class)
            ->callAction(TestAction::make('delete')->table($post));

        $token = getUndoDeletionTestToken();

        expect(Cache::get(DeleteAction::getUndoDeletionCacheKey($token)))->toBe([
            'livewire' => $component->id(),
            'user' => null,
            'records' => [Post::class => [$post->getKey()]],
            'isTable' => true,
        ]);
    });

    it('uses custom undoable seconds for the notification duration', function (): void {
        $post = Post::factory()->create();

        livewire(UndoableDeletePostsTable::class)
            ->callAction(TestAction::make('undoableWithSeconds')->table($post));

        $notifications = pullUndoDeletionTestNotifications();

        expect(findUndoDeletionTestAction($notifications))->not->toBeNull()
            ->and($notifications->first()->getDuration())->toBe(30000);
    });

    it('evaluates closures passed to `undoable()`', function (): void {
        $post = Post::factory()->create();

        livewire(UndoableDeletePostsTable::class)
            ->callAction(TestAction::make('undoableWithClosures')->table($post));

        $notifications = pullUndoDeletionTestNotifications();

        expect(findUndoDeletionTestAction($notifications))->not->toBeNull()
            ->and($notifications->first()->getDuration())->toBe(45000);
    });

    it('does not add an undo action when not undoable', function (string $actionName): void {
        $post = Post::factory()->create();

        livewire(UndoableDeletePostsTable::class)
            ->callAction(TestAction::make($actionName)->table($post));

        assertSoftDeleted($post);

        $notifications = pullUndoDeletionTestNotifications();

        expect($notifications)->not->toBeEmpty()
            ->and(findUndoDeletionTestAction($notifications))->toBeNull();
    })->with(['notUndoable', 'undoableDisabled']);

    it('does not add an undo action when the action redirects after deleting', function (): void {
        $post = Post::factory()->create();

        livewire(UndoableDeletePostsTable::class)
            ->callAction(TestAction::make('undoableWithRedirect')->table($post));

        assertSoftDeleted($post);

        expect(findUndoDeletionTestAction(pullUndoDeletionTestNotifications()))->toBeNull();
    });

    it('restores the deleted record when undone', function (): void {
        $post = Post::factory()->create();

        $component = livewire(UndoableDeletePostsTable::class)
            ->callAction(TestAction::make('delete')->table($post))
            ->assertCanNotSeeTableRecords([$post]);

        $token = getUndoDeletionTestToken();

        $component
            ->call('undoDeletedRecords', $component->id(), $token)
            ->assertNotified('Restored')
            ->assertCanSeeTableRecords([$post]);

        assertNotSoftDeleted($post);
    });

    it('restores the deleted record when the undo event is dispatched', function (): void {
        $post = Post::factory()->create();

        $component = livewire(UndoableDeletePostsTable::class)
            ->callAction(TestAction::make('delete')->table($post));

        $token = getUndoDeletionTestToken();

        $component->dispatch('filament-actions::undo-deletion', id: $component->id(), token: $token);

        assertNotSoftDeleted($post);
    });

    it('restores the deleted record when undone by the same authenticated user', function (): void {
        actingAs(User::factory()->create());

        $post = Post::factory()->create();

        $component = livewire(UndoableDeletePostsTable::class)
            ->callAction(TestAction::make('delete')->table($post));

        $token = getUndoDeletionTestToken();

        $component->call('undoDeletedRecords', $component->id(), $token);

        assertNotSoftDeleted($post);
    });
});

describe('`DeleteBulkAction`', function (): void {
    it('adds an undo action to the success notification', function (): void {
        $posts = Post::factory()->count(3)->create();

        livewire(UndoableDeletePostsTable::class)
            ->callTableBulkAction(DeleteBulkAction::class, $posts);

        $notifications = pullUndoDeletionTestNotifications();

        expect(findUndoDeletionTestAction($notifications))->not->toBeNull()
            ->and($notifications->first()->getDuration())->toBe(20000);
    });

    it('stores all deleted record keys in the cache', function (): void {
        $posts = Post::factory()->count(3)->create();

        livewire(UndoableDeletePostsTable::class)
            ->callTableBulkAction(DeleteBulkAction::class, $posts);

        $token = getUndoDeletionTestToken();
        $deletion = Cache::get(DeleteBulkAction::getUndoDeletionCacheKey($token));

        expect($deletion['records'])->toHaveKey(Post::class)
            ->and($deletion['records'][Post::class])->toEqualCanonicalizing($posts->modelKeys());
    });

    it('restores all deleted records when undone', function (): void {
        $posts = Post::factory()->count(3)->create();
        $otherPost = Post::factory()->create();
        $otherPost->delete();

        $component = livewire(UndoableDeletePostsTable::class)
            ->callTableBulkAction(DeleteBulkAction::class, $posts);

        foreach ($posts as $post) {
            assertSoftDeleted($post);
        }

        $token = getUndoDeletionTestToken();

        $component
            ->call('undoDeletedRecords', $component->id(), $token)
            ->assertNotified('Restored')
            ->assertCanSeeTableRecords($posts);

        foreach ($posts as $post) {
            assertNotSoftDeleted($post);
        }

        assertSoftDeleted($otherPost);
    });

    it('does not add an undo action when not undoable', function (): void {
        $posts = Post::factory()->count(2)->create();

        livewire(UndoableDeletePostsTable::class)
            ->callTableBulkAction('notUndoableBulk', $posts);

        foreach ($posts as $post) {
            assertSoftDeleted($post);
        }

        expect(findUndoDeletionTestAction(pullUndoDeletionTestNotifications()))->toBeNull();
    });

    it('does not add an undo action when selected records are not fetched', function (): void {
        $posts = Post::factory()->count(2)->create();

        livewire(UndoableDeletePostsTable::class)
            ->callTableBulkAction('undoableWithoutFetching', $posts);

        foreach ($posts as $post) {
            assertSoftDeleted($post);
        }

        expect(findUndoDeletionTestAction(pullUndoDeletionTestNotifications()))->toBeNull();
    });
});

describe('undoing deletions', function (): void {
    it('can only be undone once', function (): void {
        $post = Post::factory()->create();

        $component = livewire(UndoableDeletePostsTable::class)
            ->callAction(TestAction::make('delete')->table($post));

        $token = getUndoDeletionTestToken();

        $component->call('undoDeletedRecords', $component->id(), $token);

        assertNotSoftDeleted($post);

        expect(Cache::has(DeleteAction::getUndoDeletionCacheKey($token)))->toBeFalse();

        $post->delete();

        $component
            ->call('undoDeletedRecords', $component->id(), $token)
            ->assertNotified('Undo is no longer available');

        assertSoftDeleted($post);
    });

    it('fails with an unknown token', function (): void {
        $post = Post::factory()->create();

        $component = livewire(UndoableDeletePostsTable::class)
            ->callAction(TestAction::make('delete')->table($post));

        pullUndoDeletionTestNotifications();

        $component
            ->call('undoDeletedRecords', $component->id(), str_repeat('a', 40))
            ->assertNotified('Undo is no longer available');

        assertSoftDeleted($post);
    });

    it('fails with a malformed token', function (): void {
        $post = Post::factory()->create();

        $component = livewire(UndoableDeletePostsTable::class)
            ->callAction(TestAction::make('delete')->table($post));

        $token = getUndoDeletionTestToken();

        $component
            ->call('undoDeletedRecords', $component->id(), "{$token}.")
            ->assertNotified('Undo is no longer available');

        assertSoftDeleted($post);
    });

    it('ignores undo requests addressed to another component', function (): void {
        $post = Post::factory()->create();

        $component = livewire(UndoableDeletePostsTable::class)
            ->callAction(TestAction::make('delete')->table($post));

        $token = getUndoDeletionTestToken();

        $component->call('undoDeletedRecords', 'another-component-id', $token);

        assertSoftDeleted($post);

        expect(pullUndoDeletionTestNotifications())->toBeEmpty()
            ->and(Cache::has(DeleteAction::getUndoDeletionCacheKey($token)))->toBeTrue();
    });

    it('cannot be undone from another component instance', function (): void {
        $post = Post::factory()->create();

        livewire(UndoableDeletePostsTable::class)
            ->callAction(TestAction::make('delete')->table($post));

        $token = getUndoDeletionTestToken();

        $otherComponent = livewire(UndoableDeletePostsTable::class);

        $otherComponent
            ->call('undoDeletedRecords', $otherComponent->id(), $token)
            ->assertNotified('Undo is no longer available');

        assertSoftDeleted($post);
    });

    it('cannot be undone by another user', function (): void {
        actingAs(User::factory()->create());

        $post = Post::factory()->create();

        $component = livewire(UndoableDeletePostsTable::class)
            ->callAction(TestAction::make('delete')->table($post));

        $token = getUndoDeletionTestToken();

        actingAs(User::factory()->create());

        $component
            ->call('undoDeletedRecords', $component->id(), $token)
            ->assertNotified('Undo is no longer available');

        assertSoftDeleted($post);
    });

    it('cannot be undone after the undoable seconds have passed', function (): void {
        $post = Post::factory()->create();

        $component = livewire(UndoableDeletePostsTable::class)
            ->callAction(TestAction::make('delete')->table($post));

        $token = getUndoDeletionTestToken();

        $this->travel(11)->seconds();

        $component
            ->call('undoDeletedRecords', $component->id(), $token)
            ->assertNotified('Undo is no longer available');

        assertSoftDeleted($post);
    });

    it('does not restore records the user is not authorized to restore', function (): void {
        $post = Post::factory()->create();

        $component = livewire(UndoableDeletePostsTable::class)
            ->callAction(TestAction::make('delete')->table($post));

        $token = getUndoDeletionTestToken();

        UndoableDeletePostsTable::$canRestore = false;

        $component
            ->call('undoDeletedRecords', $component->id(), $token)
            ->assertNotified('Undo is no longer available');

        assertSoftDeleted($post);
    });

    it('fails when the deleted record no longer exists', function (): void {
        $post = Post::factory()->create();

        $component = livewire(UndoableDeletePostsTable::class)
            ->callAction(TestAction::make('delete')->table($post));

        $token = getUndoDeletionTestToken();

        $post->forceDelete();

        $component
            ->call('undoDeletedRecords', $component->id(), $token)
            ->assertNotified('Undo is no longer available');

        expect(Post::withTrashed()->find($post->getKey()))->toBeNull();
    });

    it('ignores records in the cache that are not soft deletable models', function (): void {
        $post = Post::factory()->create();

        $component = livewire(UndoableDeletePostsTable::class);

        $post->delete();

        $token = str_repeat('b', 40);

        Cache::put(DeleteAction::getUndoDeletionCacheKey($token), [
            'livewire' => $component->id(),
            'user' => null,
            'records' => [
                User::class => [1],
                stdClass::class => [1],
                Post::class => [$post->getKey()],
            ],
            'isTable' => true,
        ], 10);

        $component
            ->call('undoDeletedRecords', $component->id(), $token)
            ->assertNotified('Restored');

        assertNotSoftDeleted($post);
    });
});
