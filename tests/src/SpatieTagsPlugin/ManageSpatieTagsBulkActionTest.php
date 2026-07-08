<?php

use Filament\Actions\ManageSpatieTagsBulkAction;
use Filament\Actions\Testing\TestAction;
use Filament\SpatieLaravelTagsPlugin\Types\AllTagTypes;
use Filament\Tests\Fixtures\Livewire\SpatieTagsBulkActionsTable;
use Filament\Tests\Fixtures\Models\Article;
use Filament\Tests\Fixtures\Models\ThrowingTag;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Spatie\Tags\Tag;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('uses `manageTags` as the default name', function (): void {
    expect(ManageSpatieTagsBulkAction::getDefaultName())->toBe('manageTags');
});

it('defaults `getType()` to `AllTagTypes`', function (): void {
    $action = ManageSpatieTagsBulkAction::make();

    expect($action->getType())->toBeInstanceOf(AllTagTypes::class);
    expect($action->isAnyTagTypeAllowed())->toBeTrue();
});

describe('configuration', function (): void {
    it('uses the `manage-tags` label', function (): void {
        $action = ManageSpatieTagsBulkAction::make();

        expect($action->getLabel())
            ->toBe(__('filament-spatie-laravel-tags-plugin::manage-tags.label'));
    });
});

describe('suggestions', function (): void {
    it('memoizes `getTagSuggestions()` so the two inputs share a single query per request', function (): void {
        // The attach and detach `TagsInput`s both resolve their suggestions from the same action via
        // `->suggestions()` closures that the tags-input view re-runs on every render. Without the memo,
        // each call repeats the unbounded `SELECT name FROM tags` scan; the cache keeps it to one query.
        Tag::findOrCreate('Laravel');
        Tag::findOrCreate('PHP');

        $action = ManageSpatieTagsBulkAction::make();

        DB::enableQueryLog();
        DB::flushQueryLog();

        $firstSuggestions = $action->getTagSuggestions();
        $queriesAfterFirstCall = count(DB::getQueryLog());

        DB::flushQueryLog();

        $secondSuggestions = $action->getTagSuggestions();
        $queriesAfterSecondCall = count(DB::getQueryLog());

        DB::disableQueryLog();

        expect($queriesAfterFirstCall)->toBeGreaterThan(0);
        expect($queriesAfterSecondCall)->toBe(0);
        expect($secondSuggestions)->toBe($firstSuggestions);
        expect($firstSuggestions)->toContain('Laravel', 'PHP');
    });

    it('recomputes `getTagSuggestions()` after the `type()` changes', function (): void {
        Tag::findOrCreate('Laravel', 'framework');
        Tag::findOrCreate('PHP', 'language');

        $action = ManageSpatieTagsBulkAction::make()->type('framework');

        expect($action->getTagSuggestions())->toContain('Laravel')->not->toContain('PHP');

        $action->type('language');

        expect($action->getTagSuggestions())->toContain('PHP')->not->toContain('Laravel');
    });
});

describe('integration', function (): void {
    it('can attach and detach tags from the selected records in a single call', function (): void {
        $records = Article::factory()->count(3)->create();

        foreach ($records as $record) {
            $record->attachTags(['Old', 'Kept']);
        }

        livewire(SpatieTagsBulkActionsTable::class)
            ->selectTableRecords($records)
            ->callAction(TestAction::make(ManageSpatieTagsBulkAction::class)->table()->bulk(), data: [
                'tagsToAttach' => ['New'],
                'tagsToDetach' => ['Old'],
            ])
            ->assertHasNoFormErrors()
            ->assertNotified();

        foreach ($records as $record) {
            $freshRecord = Article::with('tags')->find($record->getKey());

            expect($freshRecord->getRelationValue('tags')->pluck('name')->sort()->values()->all())
                ->toBe(['Kept', 'New']);
        }
    });

    it('can attach tags without detaching any', function (): void {
        $record = Article::factory()->create();
        $record->attachTags(['Old']);

        livewire(SpatieTagsBulkActionsTable::class)
            ->selectTableRecords([$record->getKey()])
            ->callAction(TestAction::make(ManageSpatieTagsBulkAction::class)->table()->bulk(), data: [
                'tagsToAttach' => ['New'],
                'tagsToDetach' => [],
            ])
            ->assertHasNoFormErrors();

        $freshRecord = Article::with('tags')->find($record->getKey());

        expect($freshRecord->getRelationValue('tags')->pluck('name')->sort()->values()->all())
            ->toBe(['New', 'Old']);
    });

    it('can detach tags without attaching any', function (): void {
        $record = Article::factory()->create();
        $record->attachTags(['Old', 'Kept']);

        livewire(SpatieTagsBulkActionsTable::class)
            ->selectTableRecords([$record->getKey()])
            ->callAction(TestAction::make(ManageSpatieTagsBulkAction::class)->table()->bulk(), data: [
                'tagsToAttach' => [],
                'tagsToDetach' => ['Old'],
            ])
            ->assertHasNoFormErrors();

        $freshRecord = Article::with('tags')->find($record->getKey());

        expect($freshRecord->getRelationValue('tags')->pluck('name')->all())->toBe(['Kept']);
    });

    it('reports success and changes nothing when the only tags to detach do not exist', function (): void {
        $record = Article::factory()->create();
        $record->attachTags(['Kept']);

        // Same declarative semantics as the detach action: with nothing to attach and none of the
        // `tagsToDetach` resolving to existing tags via `resolveTagsForDetaching()`, the desired
        // state is already satisfied, so the action reports success rather than a processing
        // failure. Reporting failure here would make the outcome depend on whether the typed name
        // happens to match a tag elsewhere in the database, unrelated to the selected records.
        livewire(SpatieTagsBulkActionsTable::class)
            ->selectTableRecords([$record->getKey()])
            ->callAction(TestAction::make(ManageSpatieTagsBulkAction::class)->table()->bulk(), data: [
                'tagsToAttach' => [],
                'tagsToDetach' => ['Nonexistent'],
            ])
            ->assertHasNoFormErrors()
            ->assertNotified(__('filament-spatie-laravel-tags-plugin::manage-tags.notifications.updated.title'));

        $freshRecord = Article::with('tags')->find($record->getKey());

        expect($freshRecord->getRelationValue('tags')->pluck('name')->all())->toBe(['Kept']);
    });

    it('requires at least one of the two fields to be filled', function (): void {
        $records = Article::factory()->count(2)->create();

        livewire(SpatieTagsBulkActionsTable::class)
            ->selectTableRecords($records)
            ->callAction(TestAction::make(ManageSpatieTagsBulkAction::class)->table()->bulk(), data: [
                'tagsToAttach' => [],
                'tagsToDetach' => [],
            ])
            ->assertHasFormErrors([
                'tagsToAttach' => ['required_without'],
                'tagsToDetach' => ['required_without'],
            ]);
    });

    it('does not allow the same tag to be attached and detached at the same time', function (): void {
        $record = Article::factory()->create();

        livewire(SpatieTagsBulkActionsTable::class)
            ->selectTableRecords([$record->getKey()])
            ->callAction(TestAction::make(ManageSpatieTagsBulkAction::class)->table()->bulk(), data: [
                'tagsToAttach' => ['Laravel', 'PHP'],
                'tagsToDetach' => ['Laravel'],
            ])
            ->assertHasFormErrors(['tagsToDetach']);

        $freshRecord = Article::with('tags')->find($record->getKey());

        expect($freshRecord->getRelationValue('tags'))->toBeEmpty();
    });

    it('does not allow the same tag to be attached and detached when the two spellings resolve to the same tag', function (array $tagsToAttach, array $tagsToDetach): void {
        $record = Article::factory()->create();

        livewire(SpatieTagsBulkActionsTable::class)
            ->selectTableRecords([$record->getKey()])
            ->callAction(TestAction::make(ManageSpatieTagsBulkAction::class)->table()->bulk(), data: [
                'tagsToAttach' => $tagsToAttach,
                'tagsToDetach' => $tagsToDetach,
            ])
            ->assertHasFormErrors(['tagsToDetach']);

        $freshRecord = Article::with('tags')->find($record->getKey());

        expect($freshRecord->getRelationValue('tags'))->toBeEmpty();
    })->with([
        'differing only by case' => [['PHP'], ['php']],
        'name versus slug format' => [['Front End'], ['front-end']],
    ]);

    it('respects the `type()` when attaching and detaching', function (): void {
        $record = Article::factory()->create();
        $record->attachTag('Old', 'framework');
        $record->attachTag('Old');

        livewire(SpatieTagsBulkActionsTable::class, ['tagType' => 'framework'])
            ->selectTableRecords([$record->getKey()])
            ->callAction(TestAction::make(ManageSpatieTagsBulkAction::class)->table()->bulk(), data: [
                'tagsToAttach' => ['New'],
                'tagsToDetach' => ['Old'],
            ]);

        $freshRecord = Article::with('tags')->find($record->getKey());
        $remainingTags = $freshRecord->getRelationValue('tags');

        $frameworkTagNames = $remainingTags->filter(fn (Tag $tag): bool => $tag->type === 'framework')->pluck('name')->all();
        $untypedTagNames = $remainingTags->filter(fn (Tag $tag): bool => $tag->type === null)->pluck('name')->all();

        expect($frameworkTagNames)->toBe(['New']);
        expect($untypedTagNames)->toBe(['Old']);
    });

    it('treats an empty string `type()` as untyped when attaching and detaching, matching the suggestions', function (): void {
        $record = Article::factory()->create();

        // An untyped tag already attached to the record, plus a pre-existing untyped tag that is
        // not yet attached. `getTagSuggestions()` surfaces untyped tags for an empty string type
        // (via `filled()`), so attaching and detaching must operate on those same untyped tags
        // rather than a distinct empty-string type.
        $record->attachTag('Old');
        Tag::findOrCreate('New');

        livewire(SpatieTagsBulkActionsTable::class, ['tagType' => ''])
            ->selectTableRecords([$record->getKey()])
            ->callAction(TestAction::make(ManageSpatieTagsBulkAction::class)->table()->bulk(), data: [
                'tagsToAttach' => ['New'],
                'tagsToDetach' => ['Old'],
            ])
            ->assertHasNoFormErrors();

        $freshRecord = Article::with('tags')->find($record->getKey());
        $remainingTags = $freshRecord->getRelationValue('tags');

        // The untyped `Old` was detached and the untyped `New` was attached.
        expect($remainingTags->pluck('name')->all())->toBe(['New']);
        expect($remainingTags->pluck('type')->all())->toBe([null]);

        // The pre-existing untyped `New` tag was reused rather than duplicated as an empty-string type.
        expect(Tag::all()->filter(fn (Tag $tag): bool => $tag->name === 'New'))->toHaveCount(1);
    });

    it('rejects a tag entry that is not a string', function (string $field): void {
        $record = Article::factory()->create();

        // Tag names come from client-writable Livewire state, so a tampered payload can contain a
        // nested array. `nestedRecursiveRules(['string'])` must reject it during validation rather
        // than let it reach the `string`-typed resolution and conflict-validation closures and throw
        // a `TypeError`.
        livewire(SpatieTagsBulkActionsTable::class)
            ->selectTableRecords([$record->getKey()])
            ->callAction(TestAction::make(ManageSpatieTagsBulkAction::class)->table()->bulk(), data: [
                $field => [['Laravel']],
            ])
            ->assertHasFormErrors(["{$field}.0"]);

        expect(Tag::count())->toBe(0);
    })->with([
        'tagsToAttach',
        'tagsToDetach',
    ]);

    it('reports a failure notification instead of throwing when resolving the tags fails', function (): void {
        $records = Article::factory()->count(2)->create();

        // Force `resolveTagsForAttaching()` to throw to prove the action reports a graceful failure
        // instead of letting the exception escape as an uncaught error that would leave the records
        // selected with no notification.
        config(['tags.tag_model' => ThrowingTag::class]);

        livewire(SpatieTagsBulkActionsTable::class)
            ->selectTableRecords($records)
            ->callAction(TestAction::make(ManageSpatieTagsBulkAction::class)->table()->bulk(), data: [
                'tagsToAttach' => ['Laravel'],
                'tagsToDetach' => [],
            ])
            ->assertNotified();

        // Restore the real tag model so the relationship's pivot key resolves when inspecting results.
        config(['tags.tag_model' => Tag::class]);

        foreach ($records as $record) {
            expect(Article::with('tags')->find($record->getKey())->getRelationValue('tags'))->toBeEmpty();
        }
    });

});
