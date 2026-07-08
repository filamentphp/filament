<?php

use Filament\Actions\DetachSpatieTagsBulkAction;
use Filament\Actions\Testing\TestAction;
use Filament\Notifications\Notification;
use Filament\SpatieLaravelTagsPlugin\Types\AllTagTypes;
use Filament\Tests\Fixtures\Livewire\SpatieTagsBulkActionsNonTaggableTable;
use Filament\Tests\Fixtures\Livewire\SpatieTagsBulkActionsTable;
use Filament\Tests\Fixtures\Models\Article;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\TestCase;
use Illuminate\Support\Number;
use Spatie\Tags\Tag;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('uses `detachTags` as the default name', function (): void {
    expect(DetachSpatieTagsBulkAction::getDefaultName())->toBe('detachTags');
});

describe('type', function (): void {
    it('defaults `getType()` to `AllTagTypes`', function (): void {
        $action = DetachSpatieTagsBulkAction::make();

        expect($action->getType())->toBeInstanceOf(AllTagTypes::class);
        expect($action->isAnyTagTypeAllowed())->toBeTrue();
    });

    it('can set `type()` with a string', function (): void {
        $action = DetachSpatieTagsBulkAction::make()
            ->type('category');

        expect($action->getType())->toBe('category');
        expect($action->isAnyTagTypeAllowed())->toBeFalse();
    });

    it('can set `type()` with a `Closure`', function (): void {
        $action = DetachSpatieTagsBulkAction::make()
            ->type(static fn (): string => 'dynamic');

        expect($action->getType())->toBe('dynamic');
        expect($action->isAnyTagTypeAllowed())->toBeFalse();
    });

    it('can set `type()` to `null`', function (): void {
        $action = DetachSpatieTagsBulkAction::make()
            ->type(null);

        expect($action->getType())->toBeNull();
        expect($action->isAnyTagTypeAllowed())->toBeFalse();
    });
});

describe('integration', function (): void {
    it('can detach tags from the selected records', function (): void {
        $records = Article::factory()->count(3)->create();
        $unselectedRecord = Article::factory()->create();

        foreach ([...$records, $unselectedRecord] as $record) {
            $record->attachTags(['Laravel', 'PHP']);
        }

        livewire(SpatieTagsBulkActionsTable::class)
            ->selectTableRecords($records)
            ->callAction(TestAction::make(DetachSpatieTagsBulkAction::class)->table()->bulk(), data: [
                'tags' => ['Laravel'],
            ])
            ->assertHasNoFormErrors()
            ->assertNotified();

        foreach ($records as $record) {
            $freshRecord = Article::with('tags')->find($record->getKey());

            expect($freshRecord->getRelationValue('tags')->pluck('name')->all())->toBe(['PHP']);
        }

        $freshUnselectedRecord = Article::with('tags')->find($unselectedRecord->getKey());

        expect($freshUnselectedRecord->getRelationValue('tags')->pluck('name')->sort()->values()->all())
            ->toBe(['Laravel', 'PHP']);
    });

    it('requires at least one tag to be entered', function (): void {
        $records = Article::factory()->count(2)->create();

        livewire(SpatieTagsBulkActionsTable::class)
            ->selectTableRecords($records)
            ->callAction(TestAction::make(DetachSpatieTagsBulkAction::class)->table()->bulk(), data: [
                'tags' => [],
            ])
            ->assertHasFormErrors(['tags' => ['required']]);
    });

    it('does not delete the tag models from the database, only detaches them', function (): void {
        $record = Article::factory()->create();
        $record->attachTags(['Laravel']);

        livewire(SpatieTagsBulkActionsTable::class)
            ->selectTableRecords([$record->getKey()])
            ->callAction(TestAction::make(DetachSpatieTagsBulkAction::class)->table()->bulk(), data: [
                'tags' => ['Laravel'],
            ]);

        $freshRecord = Article::with('tags')->find($record->getKey());

        expect($freshRecord->getRelationValue('tags'))->toBeEmpty();
        expect(Tag::count())->toBe(1);
    });

    it('succeeds when a selected record does not have one of the tags', function (): void {
        $taggedRecord = Article::factory()->create();
        $taggedRecord->attachTags(['Laravel']);

        $untaggedRecord = Article::factory()->create();

        livewire(SpatieTagsBulkActionsTable::class)
            ->selectTableRecords([$taggedRecord->getKey(), $untaggedRecord->getKey()])
            ->callAction(TestAction::make(DetachSpatieTagsBulkAction::class)->table()->bulk(), data: [
                'tags' => ['Laravel'],
            ])
            ->assertNotified(__('filament-spatie-laravel-tags-plugin::detach-tags.notifications.detached.title'));

        $freshTaggedRecord = Article::with('tags')->find($taggedRecord->getKey());

        expect($freshTaggedRecord->getRelationValue('tags'))->toBeEmpty();
    });

    it('reports success and changes nothing when none of the entered tags exist in the database', function (): void {
        $record = Article::factory()->create();
        $record->attachTags(['Laravel']);

        // Detaching is declarative: the goal is to ensure the named tags are absent. When none of
        // the entered names resolve to existing tags via `resolveTagsForDetaching()`, that goal is
        // already satisfied, so the action reports success rather than a processing failure. This
        // mirrors detaching an existing tag from a record that does not have it (see the
        // `succeeds when a selected record does not have one of the tags` test); success must not
        // depend on whether the typed name happens to match a tag elsewhere in the database.
        livewire(SpatieTagsBulkActionsTable::class)
            ->selectTableRecords([$record->getKey()])
            ->callAction(TestAction::make(DetachSpatieTagsBulkAction::class)->table()->bulk(), data: [
                'tags' => ['Nonexistent'],
            ])
            ->assertHasNoFormErrors()
            ->assertNotified(__('filament-spatie-laravel-tags-plugin::detach-tags.notifications.detached.title'));

        $freshRecord = Article::with('tags')->find($record->getKey());

        expect($freshRecord->getRelationValue('tags')->pluck('name')->all())->toBe(['Laravel']);
    });

    it('detaches only tags of the given `type()` when one is set', function (): void {
        $record = Article::factory()->create();
        $record->attachTag('admin', 'role');
        $record->attachTag('admin');

        livewire(SpatieTagsBulkActionsTable::class, ['tagType' => 'role'])
            ->selectTableRecords([$record->getKey()])
            ->callAction(TestAction::make(DetachSpatieTagsBulkAction::class)->table()->bulk(), data: [
                'tags' => ['admin'],
            ]);

        $freshRecord = Article::with('tags')->find($record->getKey());
        $remainingTags = $freshRecord->getRelationValue('tags');

        expect($remainingTags)->toHaveCount(1);
        expect($remainingTags->first()->type)->toBeNull();
    });

    it('detaches only untyped tags when `type()` is `null`, preserving typed tags of the same name', function (): void {
        $record = Article::factory()->create();
        $record->attachTag('admin', 'role');
        $record->attachTag('admin');

        livewire(SpatieTagsBulkActionsTable::class, ['useNullType' => true])
            ->selectTableRecords([$record->getKey()])
            ->callAction(TestAction::make(DetachSpatieTagsBulkAction::class)->table()->bulk(), data: [
                'tags' => ['admin'],
            ]);

        $freshRecord = Article::with('tags')->find($record->getKey());
        $remainingTags = $freshRecord->getRelationValue('tags');

        expect($remainingTags)->toHaveCount(1);
        expect($remainingTags->first()->type)->toBe('role');
    });

    it('detaches every tag of any type matching the name when any tag type is allowed', function (): void {
        $record = Article::factory()->create();
        $record->attachTag('admin', 'role');
        $record->attachTag('admin');

        livewire(SpatieTagsBulkActionsTable::class)
            ->selectTableRecords([$record->getKey()])
            ->callAction(TestAction::make(DetachSpatieTagsBulkAction::class)->table()->bulk(), data: [
                'tags' => ['admin'],
            ]);

        $freshRecord = Article::with('tags')->find($record->getKey());

        expect($freshRecord->getRelationValue('tags'))->toBeEmpty();
    });

    it('rejects a tag entry that is not a string', function (): void {
        $record = Article::factory()->create();
        $record->attachTags(['Laravel']);

        // Tag names come from client-writable Livewire state, so a tampered payload can contain a
        // nested array. `nestedRecursiveRules(['string'])` must reject it during validation rather
        // than let it reach the `string`-typed resolution closures and throw a `TypeError`.
        livewire(SpatieTagsBulkActionsTable::class)
            ->selectTableRecords([$record->getKey()])
            ->callAction(TestAction::make(DetachSpatieTagsBulkAction::class)->table()->bulk(), data: [
                'tags' => [['Laravel']],
            ])
            ->assertHasFormErrors(['tags.0']);

        expect(Article::with('tags')->find($record->getKey())->getRelationValue('tags')->pluck('name')->all())
            ->toBe(['Laravel']);
    });
});

describe('failure and authorization paths', function (): void {
    it('detaches tags via a database cursor when `fetchSelectedRecords(false)` is set', function (): void {
        $records = Article::factory()->count(3)->create();

        foreach ($records as $record) {
            $record->attachTags(['Laravel', 'PHP']);
        }

        // `fetchSelectedRecords(false)` makes the action iterate `getSelectedRecordsQuery()->cursor()`
        // instead of an eagerly-fetched collection. The outcome must be identical to the eager path.
        livewire(SpatieTagsBulkActionsTable::class, ['shouldFetchSelectedRecords' => false])
            ->selectTableRecords($records)
            ->callAction(TestAction::make(DetachSpatieTagsBulkAction::class)->table()->bulk(), data: [
                'tags' => ['Laravel'],
            ])
            ->assertHasNoFormErrors()
            ->assertNotified(__('filament-spatie-laravel-tags-plugin::detach-tags.notifications.detached.title'));

        foreach ($records as $record) {
            expect(Article::with('tags')->find($record->getKey())->getRelationValue('tags')->pluck('name')->all())
                ->toBe(['PHP']);
        }
    });

    it('skips records that fail `authorizeIndividualRecords()` and reports a partial failure', function (): void {
        $authorizedRecords = Article::factory()->count(2)->create(['is_published' => true]);
        $unauthorizedRecord = Article::factory()->create(['is_published' => false]);

        foreach ([...$authorizedRecords, $unauthorizedRecord] as $record) {
            $record->attachTags(['Laravel']);
        }

        livewire(SpatieTagsBulkActionsTable::class, ['authorizeUsingPublished' => true])
            ->selectTableRecords([...$authorizedRecords->modelKeys(), $unauthorizedRecord->getKey()])
            ->callAction(TestAction::make(DetachSpatieTagsBulkAction::class)->table()->bulk(), data: [
                'tags' => ['Laravel'],
            ])
            ->assertHasNoFormErrors()
            ->assertNotified(
                Notification::make()
                    ->warning()
                    ->title(trans_choice('filament-spatie-laravel-tags-plugin::detach-tags.notifications.detached_partial.title', 2, [
                        'count' => Number::format(2),
                        'total' => Number::format(3),
                    ]))
                    ->body('<p>' . trans_choice('filament-spatie-laravel-tags-plugin::detach-tags.notifications.detached_partial.missing_authorization_failure_message', 1, [
                        'count' => Number::format(1),
                    ]) . '</p>')
                    ->persistent(),
            );

        foreach ($authorizedRecords as $record) {
            expect(Article::with('tags')->find($record->getKey())->getRelationValue('tags'))->toBeEmpty();
        }

        expect(Article::with('tags')->find($unauthorizedRecord->getKey())->getRelationValue('tags')->pluck('name')->all())
            ->toBe(['Laravel']);
    });

    it('reports a complete failure when `authorizeIndividualRecords()` denies every record', function (): void {
        $records = Article::factory()->count(2)->create(['is_published' => false]);

        foreach ($records as $record) {
            $record->attachTags(['Laravel']);
        }

        livewire(SpatieTagsBulkActionsTable::class, ['authorizeUsingPublished' => true])
            ->selectTableRecords($records)
            ->callAction(TestAction::make(DetachSpatieTagsBulkAction::class)->table()->bulk(), data: [
                'tags' => ['Laravel'],
            ])
            ->assertHasNoFormErrors()
            ->assertNotified(
                Notification::make()
                    ->danger()
                    ->title(trans_choice('filament-spatie-laravel-tags-plugin::detach-tags.notifications.detached_none.title', 2, [
                        'count' => Number::format(2),
                        'total' => Number::format(2),
                    ]))
                    ->body('<p>' . trans_choice('filament-spatie-laravel-tags-plugin::detach-tags.notifications.detached_none.missing_authorization_failure_message', 2, [
                        'count' => Number::format(2),
                    ]) . '</p>')
                    ->persistent(),
            );

        foreach ($records as $record) {
            expect(Article::with('tags')->find($record->getKey())->getRelationValue('tags')->pluck('name')->all())
                ->toBe(['Laravel']);
        }
    });

    it('reports a processing failure instead of throwing when a selected record is not taggable', function (): void {
        $records = Post::factory()->count(2)->create();

        // The entered tag must resolve to an existing tag, otherwise `resolveTagsForDetaching()` yields
        // no ids and the action short-circuits with success before reaching the per-record loop.
        Tag::findOrCreate('Laravel');

        // `Post` has no `tags()` relationship method, so every record hits the `method_exists()` guard
        // and is reported via `reportBulkProcessingFailure()`, exercising the processing-failure
        // notification message and its lang keys rather than throwing a `BadMethodCallException`.
        livewire(SpatieTagsBulkActionsNonTaggableTable::class)
            ->selectTableRecords($records)
            ->callAction(TestAction::make(DetachSpatieTagsBulkAction::class)->table()->bulk(), data: [
                'tags' => ['Laravel'],
            ])
            ->assertHasNoFormErrors()
            ->assertNotified(
                Notification::make()
                    ->danger()
                    ->title(trans_choice('filament-spatie-laravel-tags-plugin::detach-tags.notifications.detached_none.title', 2, [
                        'count' => Number::format(2),
                        'total' => Number::format(2),
                    ]))
                    ->body('<p>' . trans_choice('filament-spatie-laravel-tags-plugin::detach-tags.notifications.detached_none.missing_processing_failure_message', 2, [
                        'count' => Number::format(2),
                    ]) . '</p>')
                    ->persistent(),
            );
    });
});
