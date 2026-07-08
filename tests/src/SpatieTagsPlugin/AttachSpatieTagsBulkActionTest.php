<?php

use Filament\Actions\AttachSpatieTagsBulkAction;
use Filament\Actions\Testing\TestAction;
use Filament\Notifications\Notification;
use Filament\SpatieLaravelTagsPlugin\Types\AllTagTypes;
use Filament\Tests\Fixtures\Livewire\SpatieTagsBulkActionsNonTaggableTable;
use Filament\Tests\Fixtures\Livewire\SpatieTagsBulkActionsTable;
use Filament\Tests\Fixtures\Models\Article;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\ThrowingTag;
use Filament\Tests\TestCase;
use Illuminate\Support\Number;
use Spatie\Tags\Tag;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('uses `attachTags` as the default name', function (): void {
    expect(AttachSpatieTagsBulkAction::getDefaultName())->toBe('attachTags');
});

describe('type', function (): void {
    it('defaults `getType()` to `AllTagTypes`', function (): void {
        $action = AttachSpatieTagsBulkAction::make();

        expect($action->getType())->toBeInstanceOf(AllTagTypes::class);
        expect($action->isAnyTagTypeAllowed())->toBeTrue();
    });

    it('can set `type()` with a string', function (): void {
        $action = AttachSpatieTagsBulkAction::make()
            ->type('category');

        expect($action->getType())->toBe('category');
        expect($action->isAnyTagTypeAllowed())->toBeFalse();
    });

    it('can set `type()` with a `Closure`', function (): void {
        $action = AttachSpatieTagsBulkAction::make()
            ->type(static fn (): string => 'dynamic');

        expect($action->getType())->toBe('dynamic');
        expect($action->isAnyTagTypeAllowed())->toBeFalse();
    });

    it('can set `type()` to `null`', function (): void {
        $action = AttachSpatieTagsBulkAction::make()
            ->type(null);

        expect($action->getType())->toBeNull();
        expect($action->isAnyTagTypeAllowed())->toBeFalse();
    });
});

describe('suggestions', function (): void {
    it('suggests all tags when any tag type is allowed', function (): void {
        Tag::findOrCreate('Laravel', 'framework');
        Tag::findOrCreate('PHP', 'language');
        Tag::findOrCreate('Untyped');

        $suggestions = AttachSpatieTagsBulkAction::make()->getTagSuggestions();

        expect($suggestions)->toContain('Laravel');
        expect($suggestions)->toContain('PHP');
        expect($suggestions)->toContain('Untyped');
    });

    it('suggests only tags of the given type when a `type()` is set', function (): void {
        Tag::findOrCreate('Laravel', 'framework');
        Tag::findOrCreate('PHP', 'language');

        $suggestions = AttachSpatieTagsBulkAction::make()
            ->type('framework')
            ->getTagSuggestions();

        expect($suggestions)->toContain('Laravel');
        expect($suggestions)->not->toContain('PHP');
    });

    it('suggests only untyped tags when `type()` is `null`', function (): void {
        Tag::findOrCreate('Typed', 'category');
        Tag::findOrCreate('Untyped');

        $suggestions = AttachSpatieTagsBulkAction::make()
            ->type(null)
            ->getTagSuggestions();

        expect($suggestions)->toContain('Untyped');
        expect($suggestions)->not->toContain('Typed');
    });
});

describe('integration', function (): void {
    it('can attach tags to the selected records', function (): void {
        $records = Article::factory()->count(3)->create();
        $unselectedRecord = Article::factory()->create();

        livewire(SpatieTagsBulkActionsTable::class)
            ->selectTableRecords($records)
            ->callAction(TestAction::make(AttachSpatieTagsBulkAction::class)->table()->bulk(), data: [
                'tags' => ['Laravel', 'PHP'],
            ])
            ->assertHasNoFormErrors()
            ->assertNotified();

        foreach ($records as $record) {
            $freshRecord = Article::with('tags')->find($record->getKey());

            expect($freshRecord->getRelationValue('tags')->pluck('name')->sort()->values()->all())
                ->toBe(['Laravel', 'PHP']);
        }

        $freshUnselectedRecord = Article::with('tags')->find($unselectedRecord->getKey());

        expect($freshUnselectedRecord->getRelationValue('tags'))->toBeEmpty();
    });

    it('requires at least one tag to be entered', function (): void {
        $records = Article::factory()->count(2)->create();

        livewire(SpatieTagsBulkActionsTable::class)
            ->selectTableRecords($records)
            ->callAction(TestAction::make(AttachSpatieTagsBulkAction::class)->table()->bulk(), data: [
                'tags' => [],
            ])
            ->assertHasFormErrors(['tags' => ['required']]);
    });

    it('keeps the existing tags of the selected records', function (): void {
        $record = Article::factory()->create();
        $record->attachTags(['Old']);

        livewire(SpatieTagsBulkActionsTable::class)
            ->selectTableRecords([$record->getKey()])
            ->callAction(TestAction::make(AttachSpatieTagsBulkAction::class)->table()->bulk(), data: [
                'tags' => ['New'],
            ]);

        $freshRecord = Article::with('tags')->find($record->getKey());

        expect($freshRecord->getRelationValue('tags')->pluck('name')->sort()->values()->all())
            ->toBe(['New', 'Old']);
    });

    it('does not duplicate tags that are already attached to a selected record', function (): void {
        $record = Article::factory()->create();
        $record->attachTags(['Laravel']);

        livewire(SpatieTagsBulkActionsTable::class)
            ->selectTableRecords([$record->getKey()])
            ->callAction(TestAction::make(AttachSpatieTagsBulkAction::class)->table()->bulk(), data: [
                'tags' => ['Laravel', 'PHP'],
            ]);

        $freshRecord = Article::with('tags')->find($record->getKey());

        expect($freshRecord->getRelationValue('tags'))->toHaveCount(2);
        expect(Tag::count())->toBe(2);
    });

    it('reuses existing tags instead of creating new ones', function (): void {
        $existingTag = Tag::findOrCreate('Laravel');
        $record = Article::factory()->create();

        livewire(SpatieTagsBulkActionsTable::class)
            ->selectTableRecords([$record->getKey()])
            ->callAction(TestAction::make(AttachSpatieTagsBulkAction::class)->table()->bulk(), data: [
                'tags' => ['Laravel'],
            ]);

        $freshRecord = Article::with('tags')->find($record->getKey());

        expect($freshRecord->getRelationValue('tags')->pluck('id')->all())
            ->toBe([$existingTag->getKey()]);
        expect(Tag::count())->toBe(1);
    });

    it('can attach tags of a specific `type()`', function (): void {
        $records = Article::factory()->count(2)->create();

        livewire(SpatieTagsBulkActionsTable::class, ['tagType' => 'framework'])
            ->selectTableRecords($records)
            ->callAction(TestAction::make(AttachSpatieTagsBulkAction::class)->table()->bulk(), data: [
                'tags' => ['Laravel'],
            ]);

        foreach ($records as $record) {
            $freshRecord = Article::with('tags')->find($record->getKey());
            $tags = $freshRecord->getRelationValue('tags');

            expect($tags)->toHaveCount(1);
            expect($tags->first()->type)->toBe('framework');
            expect($tags->first()->name)->toBe('Laravel');
        }
    });

    it('does not attach an existing tag of another type when a `type()` is set', function (): void {
        $privilegedTag = Tag::findOrCreate('admin', 'role');
        $record = Article::factory()->create();

        livewire(SpatieTagsBulkActionsTable::class, ['tagType' => 'category'])
            ->selectTableRecords([$record->getKey()])
            ->callAction(TestAction::make(AttachSpatieTagsBulkAction::class)->table()->bulk(), data: [
                'tags' => ['admin'],
            ]);

        $freshRecord = Article::with('tags')->find($record->getKey());
        $attachedTags = $freshRecord->getRelationValue('tags');

        expect($attachedTags)->toHaveCount(1);
        expect($attachedTags->first()->type)->toBe('category');
        expect($attachedTags->first()->getKey())->not->toBe($privilegedTag->getKey());
    });

    it('attaches a tag as untyped when `type()` is `null`, and does not attach a privileged tag of the same name', function (): void {
        $privilegedTag = Tag::findOrCreate('admin', 'role');
        $record = Article::factory()->create();

        livewire(SpatieTagsBulkActionsTable::class, ['useNullType' => true])
            ->selectTableRecords([$record->getKey()])
            ->callAction(TestAction::make(AttachSpatieTagsBulkAction::class)->table()->bulk(), data: [
                'tags' => ['admin'],
            ]);

        $freshRecord = Article::with('tags')->find($record->getKey());
        $attachedTags = $freshRecord->getRelationValue('tags');

        expect($attachedTags)->toHaveCount(1);
        expect($attachedTags->first()->type)->toBeNull();
        expect($attachedTags->first()->getKey())->not->toBe($privilegedTag->getKey());
    });

    it('attaches every existing tag of any type matching the name when any tag type is allowed', function (): void {
        $typedTag = Tag::findOrCreate('admin', 'role');
        $untypedTag = Tag::findOrCreate('admin');
        $record = Article::factory()->create();

        livewire(SpatieTagsBulkActionsTable::class)
            ->selectTableRecords([$record->getKey()])
            ->callAction(TestAction::make(AttachSpatieTagsBulkAction::class)->table()->bulk(), data: [
                'tags' => ['admin'],
            ]);

        $freshRecord = Article::with('tags')->find($record->getKey());
        $attachedTagIds = $freshRecord->getRelationValue('tags')->pluck('id')->all();

        expect($attachedTagIds)->toContain($typedTag->getKey());
        expect($attachedTagIds)->toContain($untypedTag->getKey());
    });

    it('rejects a tag entry that is not a string', function (): void {
        $record = Article::factory()->create();

        // Tag names come from client-writable Livewire state, so a tampered payload can contain a
        // nested array. `nestedRecursiveRules(['string'])` must reject it during validation rather
        // than let it reach the `string`-typed resolution closures and throw a `TypeError`.
        livewire(SpatieTagsBulkActionsTable::class)
            ->selectTableRecords([$record->getKey()])
            ->callAction(TestAction::make(AttachSpatieTagsBulkAction::class)->table()->bulk(), data: [
                'tags' => [['Laravel']],
            ])
            ->assertHasFormErrors(['tags.0']);

        expect(Tag::count())->toBe(0);
    });

    it('reports a failure notification instead of throwing when resolving the tags fails', function (): void {
        $records = Article::factory()->count(2)->create();

        // Force `resolveTagsForAttaching()` to throw to prove the action reports a graceful failure
        // instead of letting the exception escape as an uncaught error that would leave the records
        // selected with no notification.
        config(['tags.tag_model' => ThrowingTag::class]);

        livewire(SpatieTagsBulkActionsTable::class)
            ->selectTableRecords($records)
            ->callAction(TestAction::make(AttachSpatieTagsBulkAction::class)->table()->bulk(), data: [
                'tags' => ['Laravel'],
            ])
            ->assertNotified();

        // Restore the real tag model so the relationship's pivot key resolves when inspecting results.
        config(['tags.tag_model' => Tag::class]);

        foreach ($records as $record) {
            expect(Article::with('tags')->find($record->getKey())->getRelationValue('tags'))->toBeEmpty();
        }
    });
});

describe('failure and authorization paths', function (): void {
    it('attaches tags via a database cursor when `fetchSelectedRecords(false)` is set', function (): void {
        $records = Article::factory()->count(3)->create();

        // `fetchSelectedRecords(false)` makes the action iterate `getSelectedRecordsQuery()->cursor()`
        // instead of an eagerly-fetched collection. The outcome must be identical to the eager path.
        livewire(SpatieTagsBulkActionsTable::class, ['shouldFetchSelectedRecords' => false])
            ->selectTableRecords($records)
            ->callAction(TestAction::make(AttachSpatieTagsBulkAction::class)->table()->bulk(), data: [
                'tags' => ['Laravel'],
            ])
            ->assertHasNoFormErrors()
            ->assertNotified(__('filament-spatie-laravel-tags-plugin::attach-tags.notifications.attached.title'));

        foreach ($records as $record) {
            expect(Article::with('tags')->find($record->getKey())->getRelationValue('tags')->pluck('name')->all())
                ->toBe(['Laravel']);
        }
    });

    it('skips records that fail `authorizeIndividualRecords()` and reports a partial failure', function (): void {
        $authorizedRecords = Article::factory()->count(2)->create(['is_published' => true]);
        $unauthorizedRecord = Article::factory()->create(['is_published' => false]);

        livewire(SpatieTagsBulkActionsTable::class, ['authorizeUsingPublished' => true])
            ->selectTableRecords([...$authorizedRecords->modelKeys(), $unauthorizedRecord->getKey()])
            ->callAction(TestAction::make(AttachSpatieTagsBulkAction::class)->table()->bulk(), data: [
                'tags' => ['Laravel'],
            ])
            ->assertHasNoFormErrors()
            ->assertNotified(
                Notification::make()
                    ->warning()
                    ->title(trans_choice('filament-spatie-laravel-tags-plugin::attach-tags.notifications.attached_partial.title', 2, [
                        'count' => Number::format(2),
                        'total' => Number::format(3),
                    ]))
                    ->body('<p>' . trans_choice('filament-spatie-laravel-tags-plugin::attach-tags.notifications.attached_partial.missing_authorization_failure_message', 1, [
                        'count' => Number::format(1),
                    ]) . '</p>')
                    ->persistent(),
            );

        foreach ($authorizedRecords as $record) {
            expect(Article::with('tags')->find($record->getKey())->getRelationValue('tags')->pluck('name')->all())
                ->toBe(['Laravel']);
        }

        expect(Article::with('tags')->find($unauthorizedRecord->getKey())->getRelationValue('tags'))->toBeEmpty();
    });

    it('reports a complete failure when `authorizeIndividualRecords()` denies every record', function (): void {
        $records = Article::factory()->count(2)->create(['is_published' => false]);

        livewire(SpatieTagsBulkActionsTable::class, ['authorizeUsingPublished' => true])
            ->selectTableRecords($records)
            ->callAction(TestAction::make(AttachSpatieTagsBulkAction::class)->table()->bulk(), data: [
                'tags' => ['Laravel'],
            ])
            ->assertHasNoFormErrors()
            ->assertNotified(
                Notification::make()
                    ->danger()
                    ->title(trans_choice('filament-spatie-laravel-tags-plugin::attach-tags.notifications.attached_none.title', 2, [
                        'count' => Number::format(2),
                        'total' => Number::format(2),
                    ]))
                    ->body('<p>' . trans_choice('filament-spatie-laravel-tags-plugin::attach-tags.notifications.attached_none.missing_authorization_failure_message', 2, [
                        'count' => Number::format(2),
                    ]) . '</p>')
                    ->persistent(),
            );

        foreach ($records as $record) {
            expect(Article::with('tags')->find($record->getKey())->getRelationValue('tags'))->toBeEmpty();
        }
    });

    it('reports a processing failure instead of throwing when a selected record is not taggable', function (): void {
        $records = Post::factory()->count(2)->create();

        // `Post` has no `tags()` relationship method, so every record hits the `method_exists()` guard
        // and is reported via `reportBulkProcessingFailure()`, exercising the processing-failure
        // notification message and its lang keys rather than throwing a `BadMethodCallException`.
        livewire(SpatieTagsBulkActionsNonTaggableTable::class)
            ->selectTableRecords($records)
            ->callAction(TestAction::make(AttachSpatieTagsBulkAction::class)->table()->bulk(), data: [
                'tags' => ['Laravel'],
            ])
            ->assertHasNoFormErrors()
            ->assertNotified(
                Notification::make()
                    ->danger()
                    ->title(trans_choice('filament-spatie-laravel-tags-plugin::attach-tags.notifications.attached_none.title', 2, [
                        'count' => Number::format(2),
                        'total' => Number::format(2),
                    ]))
                    ->body('<p>' . trans_choice('filament-spatie-laravel-tags-plugin::attach-tags.notifications.attached_none.missing_processing_failure_message', 2, [
                        'count' => Number::format(2),
                    ]) . '</p>')
                    ->persistent(),
            );
    });
});
