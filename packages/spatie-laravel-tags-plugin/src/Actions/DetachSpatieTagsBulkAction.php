<?php

namespace Filament\Actions;

use Filament\Actions\Concerns\CanCustomizeProcess;
use Filament\Actions\Concerns\InteractsWithSpatieTags;
use Filament\Forms\Components\TagsInput;
use Filament\SpatieLaravelTagsPlugin\Types\AllTagTypes;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Number;
use Throwable;

class DetachSpatieTagsBulkAction extends BulkAction
{
    use CanCustomizeProcess;
    use InteractsWithSpatieTags;

    public static function getDefaultName(): ?string
    {
        return 'detachTags';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->type(new AllTagTypes);

        $this->label(__('filament-spatie-laravel-tags-plugin::detach-tags.label'));

        $this->modalHeading(fn (): string => __('filament-spatie-laravel-tags-plugin::detach-tags.modal.heading', ['label' => $this->getTitleCasePluralModelLabel()]));

        $this->modalSubmitActionLabel(__('filament-spatie-laravel-tags-plugin::detach-tags.modal.actions.detach.label'));

        $this->successNotificationTitle(__('filament-spatie-laravel-tags-plugin::detach-tags.notifications.detached.title'));

        $this->failureNotificationTitle(function (int $successCount, int $totalCount): string {
            if ($successCount) {
                return trans_choice('filament-spatie-laravel-tags-plugin::detach-tags.notifications.detached_partial.title', $successCount, [
                    'count' => Number::format($successCount),
                    'total' => Number::format($totalCount),
                ]);
            }

            return trans_choice('filament-spatie-laravel-tags-plugin::detach-tags.notifications.detached_none.title', $totalCount, [
                'count' => Number::format($totalCount),
                'total' => Number::format($totalCount),
            ]);
        });

        $this->missingBulkAuthorizationFailureNotificationMessage(function (int $failureCount, int $totalCount): string {
            return trans_choice(
                ($failureCount === $totalCount)
                    ? 'filament-spatie-laravel-tags-plugin::detach-tags.notifications.detached_none.missing_authorization_failure_message'
                    : 'filament-spatie-laravel-tags-plugin::detach-tags.notifications.detached_partial.missing_authorization_failure_message',
                $failureCount,
                ['count' => Number::format($failureCount)],
            );
        });

        $this->missingBulkProcessingFailureNotificationMessage(function (int $failureCount, int $totalCount): string {
            return trans_choice(
                ($failureCount === $totalCount)
                    ? 'filament-spatie-laravel-tags-plugin::detach-tags.notifications.detached_none.missing_processing_failure_message'
                    : 'filament-spatie-laravel-tags-plugin::detach-tags.notifications.detached_partial.missing_processing_failure_message',
                $failureCount,
                ['count' => Number::format($failureCount)],
            );
        });

        $this->defaultColor('danger');

        $this->icon(Heroicon::Tag);

        $this->modalIcon(Heroicon::OutlinedTag);

        $this->modalWidth(Width::Large);

        $this->schema(fn (DetachSpatieTagsBulkAction $action): array => [
            TagsInput::make('tags')
                ->label(__('filament-spatie-laravel-tags-plugin::detach-tags.modal.form.tags.label'))
                ->suggestions(static fn (): array => $action->getTagSuggestions())
                // Tag names come from client-writable Livewire state. Validate each entry as a
                // `string` so a tampered payload with a nested array fails validation instead of
                // reaching the `string`-typed resolution closures and throwing a `TypeError`.
                ->nestedRecursiveRules(['string'])
                ->required(),
        ]);

        $this->action(function (): void {
            $this->process(static function (DetachSpatieTagsBulkAction $action, array $data, EloquentCollection | Collection | LazyCollection $records): void {
                if (! $action->shouldFetchSelectedRecords()) {
                    $records = $action->getSelectedRecordsQuery()->cursor();
                }

                try {
                    // Resolving the tags runs find-or-create queries before any record is processed.
                    // If that throws (e.g. a `findOrCreate()` race against a user-added unique index,
                    // or a transient database error), report a complete failure so the user gets the
                    // failure notification instead of an uncaught error, mirroring `DeleteBulkAction`.
                    $tagIds = $action->resolveTagsForDetaching($data['tags'] ?? [])->pluck('id')->all();
                } catch (Throwable $exception) {
                    $action->reportCompleteBulkProcessingFailure();

                    report($exception);

                    return;
                }

                if (empty($tagIds)) {
                    return;
                }

                $isFirstException = true;

                $records->each(static function (Model $record) use ($action, $tagIds, &$isFirstException): void {
                    if (! method_exists($record, 'tags')) {
                        $action->reportBulkProcessingFailure();

                        return;
                    }

                    try {
                        $record->tags()->detach($tagIds);
                        $record->unsetRelation('tags');
                    } catch (Throwable $exception) {
                        $action->reportBulkProcessingFailure();

                        if ($isFirstException) {
                            // Only report the first exception so as to not flood error logs. Even
                            // if Filament did not catch exceptions like this, only the first
                            // would be reported as the rest of the process would be halted.
                            report($exception);

                            $isFirstException = false;
                        }
                    }
                });
            });
        });

        $this->deselectRecordsAfterCompletion();
    }
}
