<?php

namespace Filament\Actions;

use Closure;
use Filament\Actions\Concerns\CanCustomizeProcess;
use Filament\Actions\Concerns\InteractsWithSpatieTags;
use Filament\Forms\Components\TagsInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\SpatieLaravelTagsPlugin\Types\AllTagTypes;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Number;
use Illuminate\Support\Str;
use Throwable;

class ManageSpatieTagsBulkAction extends BulkAction
{
    use CanCustomizeProcess;
    use InteractsWithSpatieTags;

    public static function getDefaultName(): ?string
    {
        return 'manageTags';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->type(new AllTagTypes);

        $this->label(__('filament-spatie-laravel-tags-plugin::manage-tags.label'));

        $this->modalHeading(fn (): string => __('filament-spatie-laravel-tags-plugin::manage-tags.modal.heading', ['label' => $this->getTitleCasePluralModelLabel()]));

        $this->modalSubmitActionLabel(__('filament-spatie-laravel-tags-plugin::manage-tags.modal.actions.save.label'));

        $this->successNotificationTitle(__('filament-spatie-laravel-tags-plugin::manage-tags.notifications.updated.title'));

        $this->failureNotificationTitle(function (int $successCount, int $totalCount): string {
            if ($successCount) {
                return trans_choice('filament-spatie-laravel-tags-plugin::manage-tags.notifications.updated_partial.title', $successCount, [
                    'count' => Number::format($successCount),
                    'total' => Number::format($totalCount),
                ]);
            }

            return trans_choice('filament-spatie-laravel-tags-plugin::manage-tags.notifications.updated_none.title', $totalCount, [
                'count' => Number::format($totalCount),
                'total' => Number::format($totalCount),
            ]);
        });

        $this->missingBulkAuthorizationFailureNotificationMessage(function (int $failureCount, int $totalCount): string {
            return trans_choice(
                ($failureCount === $totalCount)
                    ? 'filament-spatie-laravel-tags-plugin::manage-tags.notifications.updated_none.missing_authorization_failure_message'
                    : 'filament-spatie-laravel-tags-plugin::manage-tags.notifications.updated_partial.missing_authorization_failure_message',
                $failureCount,
                ['count' => Number::format($failureCount)],
            );
        });

        $this->missingBulkProcessingFailureNotificationMessage(function (int $failureCount, int $totalCount): string {
            return trans_choice(
                ($failureCount === $totalCount)
                    ? 'filament-spatie-laravel-tags-plugin::manage-tags.notifications.updated_none.missing_processing_failure_message'
                    : 'filament-spatie-laravel-tags-plugin::manage-tags.notifications.updated_partial.missing_processing_failure_message',
                $failureCount,
                ['count' => Number::format($failureCount)],
            );
        });

        $this->icon(Heroicon::Tag);

        $this->modalIcon(Heroicon::OutlinedTag);

        $this->modalWidth(Width::Large);

        $this->schema(fn (ManageSpatieTagsBulkAction $action): array => [
            TagsInput::make('tagsToAttach')
                ->label(__('filament-spatie-laravel-tags-plugin::manage-tags.modal.form.tags_to_attach.label'))
                ->suggestions(static fn (): array => $action->getTagSuggestions())
                // Tag names come from client-writable Livewire state. Validate each entry as a
                // `string` so a tampered payload with a nested array fails validation instead of
                // reaching the `string`-typed resolution closures and throwing a `TypeError`.
                ->nestedRecursiveRules(['string'])
                ->requiredWithout('tagsToDetach'),
            TagsInput::make('tagsToDetach')
                ->label(__('filament-spatie-laravel-tags-plugin::manage-tags.modal.form.tags_to_detach.label'))
                ->suggestions(static fn (): array => $action->getTagSuggestions())
                ->nestedRecursiveRules(['string'])
                ->requiredWithout('tagsToAttach')
                ->rules([
                    static fn (Get $get): Closure => static function (string $attribute, mixed $value, Closure $fail) use ($get): void {
                        // Tags resolve by name *or* slug (see `InteractsWithSpatieTags` and
                        // Spatie's `findFromStringOfAnyType()`), so `PHP`/`php` or `Front End`/`front-end`
                        // point at the same tag. Comparing slugs catches conflicts that a raw string
                        // comparison would miss and silently detach a tag the user asked to attach.
                        //
                        // Both fields come from client-writable Livewire state. `nestedRecursiveRules(['string'])`
                        // reports any non-string entry as its own validation error, but this closure still runs
                        // over the raw arrays, so filter to strings first to keep a tampered nested-array payload
                        // from reaching the `string`-typed callbacks and throwing a `TypeError`.
                        $tagsToAttachSlugs = array_map(
                            static fn (string $tag): string => Str::slug($tag),
                            array_filter($get('tagsToAttach') ?? [], 'is_string'),
                        );

                        $conflictingTags = array_filter(
                            array_filter($value ?? [], 'is_string'),
                            static fn (string $tag): bool => in_array(Str::slug($tag), $tagsToAttachSlugs, strict: true),
                        );

                        if (empty($conflictingTags)) {
                            return;
                        }

                        $fail(__('filament-spatie-laravel-tags-plugin::manage-tags.modal.form.tags_to_detach.validation.attached_and_detached', [
                            'tags' => implode(', ', $conflictingTags),
                        ]));
                    },
                ]),
        ]);

        $this->action(function (): void {
            $this->process(static function (ManageSpatieTagsBulkAction $action, array $data, EloquentCollection | Collection | LazyCollection $records): void {
                if (! $action->shouldFetchSelectedRecords()) {
                    $records = $action->getSelectedRecordsQuery()->cursor();
                }

                try {
                    // Resolving the tags runs find-or-create queries before any record is processed.
                    // If that throws (e.g. a `findOrCreate()` race against a user-added unique index,
                    // or a transient database error), report a complete failure so the user gets the
                    // failure notification instead of an uncaught error, mirroring `DeleteBulkAction`.
                    $tagIdsToAttach = $action->resolveTagsForAttaching($data['tagsToAttach'] ?? [])->pluck('id')->all();

                    $tagIdsToDetach = $action->resolveTagsForDetaching($data['tagsToDetach'] ?? [])->pluck('id')->all();
                } catch (Throwable $exception) {
                    $action->reportCompleteBulkProcessingFailure();

                    report($exception);

                    return;
                }

                if (empty($tagIdsToAttach) && empty($tagIdsToDetach)) {
                    return;
                }

                $isFirstException = true;

                $records->each(static function (Model $record) use ($action, $tagIdsToAttach, $tagIdsToDetach, &$isFirstException): void {
                    if (! method_exists($record, 'tags')) {
                        $action->reportBulkProcessingFailure();

                        return;
                    }

                    try {
                        // Attaching and detaching are two dependent writes. Wrap them in a
                        // transaction so a failure between them cannot leave the record
                        // half-updated while it is reported as a failure that implies nothing
                        // changed. Nests via savepoints under an opt-in `->databaseTransaction()`.
                        DB::transaction(static function () use ($record, $tagIdsToAttach, $tagIdsToDetach): void {
                            if (! empty($tagIdsToAttach)) {
                                $record->tags()->syncWithoutDetaching($tagIdsToAttach);
                            }

                            if (! empty($tagIdsToDetach)) {
                                $record->tags()->detach($tagIdsToDetach);
                            }
                        });

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
