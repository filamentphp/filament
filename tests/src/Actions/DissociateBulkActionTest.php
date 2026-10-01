<?php

use Filament\Actions\DissociateBulkAction;
use Filament\Actions\Testing\TestAction;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Fixtures\Resources\Users\Pages\EditUser;
use Filament\Tests\Fixtures\Resources\Users\RelationManagers\PostsWithDissociateBulkActionRelationManager;
use Filament\Tests\Panels\Resources\TestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Exceptions;

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

it('returns `dissociate` from `getDefaultName()`', function (): void {
    expect(DissociateBulkAction::getDefaultName())->toBe('dissociate');
});

it('counts event vetoes and authorization failures separately when dissociating', function (string $event, ?int $chunkSize, bool $throwsException): void {
    $user = User::factory()->create();
    [$allowedPost, $vetoedPost, $unauthorizedPost, $unselectedPost] = Post::factory()->count(4)->create(['author_id' => $user->id])->all();
    $attemptedRecords = [];
    $counts = collect();
    $transactionLevel = DB::transactionLevel();
    Exceptions::fake();

    Post::{$event}(static function (Post $post) use ($vetoedPost, &$attemptedRecords, $throwsException): bool {
        $attemptedRecords[] = $post->getKey();

        if ($throwsException && $post->is($vetoedPost)) {
            throw new RuntimeException('Dissociation prevented');
        }

        return ! $post->is($vetoedPost);
    });

    DissociateBulkAction::configureUsing(
        static fn (DissociateBulkAction $action) => $action
            ->databaseTransaction()
            ->chunkSelectedRecords($chunkSize)
            ->authorizeIndividualRecords(static fn (Post $record): bool => ! $record->is($unauthorizedPost))
            ->failureNotificationTitle(static function (int $successCount, int $failureCount, int $totalCount, int $missingProcessingFailureMessageCount, array $processingFailureMessages) use ($counts): string {
                $counts->push($successCount, $failureCount, $totalCount, $missingProcessingFailureMessageCount, $processingFailureMessages);

                return 'Some posts could not be dissociated';
            }),
        during: static fn () => livewire(PostsWithDissociateBulkActionRelationManager::class, ['ownerRecord' => $user, 'pageClass' => EditUser::class])
            ->callTableBulkAction(DissociateBulkAction::class, [$allowedPost, $vetoedPost, $unauthorizedPost])
            ->assertDispatched('deselectAllTableRecords'),
    );

    expect($allowedPost->refresh()->author_id)->toBeNull()
        ->and($vetoedPost->refresh()->author_id)->toBe($user->id)
        ->and($unauthorizedPost->refresh()->author_id)->toBe($user->id)
        ->and($unselectedPost->refresh()->author_id)->toBe($user->id)
        ->and($attemptedRecords)->toEqualCanonicalizing([$allowedPost->getKey(), $vetoedPost->getKey()])
        ->and($counts->all())->toBe([1, 2, 3, 1, []])
        ->and(DB::transactionLevel())->toBe($transactionLevel);

    Exceptions::assertReportedCount($throwsException ? 1 : 0);
})->with(['saving', 'updating'])->with([null, 2])->with([false, true]);

it('shows successful and partially vetoed dissociations in the browser', function (bool $hasVeto): void {
    Artisan::call('filament:assets');
    $user = auth()->user();
    $allowedPost = Post::factory()->create(['author_id' => $user->id, 'title' => 'Summer reading list']);
    $otherPost = Post::factory()->create(['author_id' => $user->id, 'title' => 'Editorial review']);

    Post::saving(static fn (Post $post): bool => (! $hasVeto) || (! $post->is($otherPost)));

    $browser = visit('/dissociate-bulk-action-browser-test')->inDarkMode();

    $browser
        ->assertScript(<<<'JS'
            (() => {
                const dropdown = document.querySelector('[data-testid="bulk-actions-dropdown"]')

                return dropdown?.dataset.dropdownOnly === 'array'
                    && dropdown.dataset.closureOnly === 'closure'
                    && dropdown.dataset.mergePrecedence === 'first'
                    && ! dropdown.hasAttribute('data-group-only')
                    && dropdown.querySelector('[data-group-only="group"]') !== null
            })()
            JS, true)
        ->check('input[type="checkbox"][value="' . $allowedPost->getKey() . '"]')
        ->check('input[type="checkbox"][value="' . $otherPost->getKey() . '"]')
        ->click('[data-testid="bulk-actions-trigger"]:visible')
        ->assertVisible('[data-testid="dissociate-posts"]');

    foreach (['dark', 'light'] as $theme) {
        $browser->script("window.dispatchEvent(new CustomEvent('theme-changed', { detail: '{$theme}' }))");
        $browser->assertScript('document.getAnimations().every((animation) => animation.effect.getTiming().iterations === Infinity || animation.playState === "finished")')
            ->assertNoAccessibilityIssues();
    }

    $browser
        ->click('[data-testid="dissociate-posts"]')
        ->click('[data-testid="confirm-dissociate"]')
        ->assertMissing('input[type="checkbox"][value="' . $allowedPost->getKey() . '"]');

    if ($hasVeto) {
        $browser->assertSee('1 dissociated, 1 failed')
            ->assertNotChecked('input[type="checkbox"][value="' . $otherPost->getKey() . '"]');
    } else {
        $browser->assertMissing('input[type="checkbox"][value="' . $otherPost->getKey() . '"]');
    }

    foreach (['dark', 'light'] as $theme) {
        $browser->script("window.dispatchEvent(new CustomEvent('theme-changed', { detail: '{$theme}' }))");
        $browser->assertScript('document.getAnimations().every((animation) => animation.effect.getTiming().iterations === Infinity || animation.playState === "finished")')
            ->assertNoAccessibilityIssues();
    }

    expect($allowedPost->refresh()->author_id)->toBeNull()
        ->and($otherPost->refresh()->author_id)->toBe($hasVeto ? $user->id : null);
})->with([false, true]);
