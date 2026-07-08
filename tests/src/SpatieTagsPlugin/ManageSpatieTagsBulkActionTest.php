<?php

use Filament\Actions\ManageSpatieTagsBulkAction;
use Filament\Actions\Testing\TestAction;
use Filament\SpatieLaravelTagsPlugin\Types\AllTagTypes;
use Filament\Tests\Fixtures\Livewire\SpatieTagsBulkActionsTable;
use Filament\Tests\Fixtures\Models\Article;
use Filament\Tests\TestCase;
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

});
