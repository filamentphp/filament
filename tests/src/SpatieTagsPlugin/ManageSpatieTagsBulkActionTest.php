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
    it('allows attaching and detaching by default', function (): void {
        $action = ManageSpatieTagsBulkAction::make();

        expect($action->canAttachTags())->toBeTrue();
        expect($action->canDetachTags())->toBeTrue();
    });

    it('can disable attaching with `attachable(false)`', function (): void {
        $action = ManageSpatieTagsBulkAction::make()
            ->attachable(false);

        expect($action->canAttachTags())->toBeFalse();
        expect($action->canDetachTags())->toBeTrue();
    });

    it('can disable detaching with `detachable(false)`', function (): void {
        $action = ManageSpatieTagsBulkAction::make()
            ->detachable(false);

        expect($action->canAttachTags())->toBeTrue();
        expect($action->canDetachTags())->toBeFalse();
    });

    it('can set `attachable()` and `detachable()` with a `Closure`', function (): void {
        $action = ManageSpatieTagsBulkAction::make()
            ->attachable(static fn (): bool => false)
            ->detachable(static fn (): bool => true);

        expect($action->canAttachTags())->toBeFalse();
        expect($action->canDetachTags())->toBeTrue();
    });

    it('uses the `manage-tags` label by default', function (): void {
        $action = ManageSpatieTagsBulkAction::make();

        expect($action->getLabel())
            ->toBe(__('filament-spatie-laravel-tags-plugin::manage-tags.label'));
    });

    it('uses the `attach-tags` label when detaching is disabled', function (): void {
        $action = ManageSpatieTagsBulkAction::make()
            ->detachable(false);

        expect($action->getLabel())
            ->toBe(__('filament-spatie-laravel-tags-plugin::attach-tags.label'));
    });

    it('uses the `detach-tags` label when attaching is disabled', function (): void {
        $action = ManageSpatieTagsBulkAction::make()
            ->attachable(false);

        expect($action->getLabel())
            ->toBe(__('filament-spatie-laravel-tags-plugin::detach-tags.label'));
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

    it('only attaches tags when detaching is disabled, even if detach data is submitted', function (): void {
        $record = Article::factory()->create();
        $record->attachTags(['Old']);

        livewire(SpatieTagsBulkActionsTable::class, ['isManageActionDetachable' => false])
            ->selectTableRecords([$record->getKey()])
            ->callAction(TestAction::make(ManageSpatieTagsBulkAction::class)->table()->bulk(), data: [
                'tagsToAttach' => ['New'],
                'tagsToDetach' => ['Old'],
            ]);

        $freshRecord = Article::with('tags')->find($record->getKey());

        expect($freshRecord->getRelationValue('tags')->pluck('name')->sort()->values()->all())
            ->toBe(['New', 'Old']);
    });

    it('only detaches tags when attaching is disabled, even if attach data is submitted', function (): void {
        $record = Article::factory()->create();
        $record->attachTags(['Old']);

        livewire(SpatieTagsBulkActionsTable::class, ['isManageActionAttachable' => false])
            ->selectTableRecords([$record->getKey()])
            ->callAction(TestAction::make(ManageSpatieTagsBulkAction::class)->table()->bulk(), data: [
                'tagsToAttach' => ['New'],
                'tagsToDetach' => ['Old'],
            ]);

        $freshRecord = Article::with('tags')->find($record->getKey());

        expect($freshRecord->getRelationValue('tags'))->toBeEmpty();
        expect(Tag::query()->where('name->en', 'New')->exists())->toBeFalse();
    });

    it('requires the attach field when detaching is disabled', function (): void {
        $records = Article::factory()->count(2)->create();

        livewire(SpatieTagsBulkActionsTable::class, ['isManageActionDetachable' => false])
            ->selectTableRecords($records)
            ->callAction(TestAction::make(ManageSpatieTagsBulkAction::class)->table()->bulk(), data: [
                'tagsToAttach' => [],
            ])
            ->assertHasFormErrors(['tagsToAttach' => ['required_without']]);
    });

    it('is hidden when both attaching and detaching are disabled', function (): void {
        Article::factory()->create();

        livewire(SpatieTagsBulkActionsTable::class, [
            'isManageActionAttachable' => false,
            'isManageActionDetachable' => false,
        ])
            ->assertActionHidden(TestAction::make(ManageSpatieTagsBulkAction::class)->table()->bulk());
    });
});
