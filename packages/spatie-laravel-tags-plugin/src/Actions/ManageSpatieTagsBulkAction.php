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
                ->requiredWithout('tagsToDetach'),
            TagsInput::make('tagsToDetach')
                ->label(__('filament-spatie-laravel-tags-plugin::manage-tags.modal.form.tags_to_detach.label'))
                ->suggestions(static fn (): array => $action->getTagSuggestions())
                ->requiredWithout('tagsToAttach')
                ->rules([
                    static fn (Get $get): Closure => static function (string $attribute, mixed $value, Closure $fail) use ($get): void {
                        // Tags resolve by name *or* slug (see `InteractsWithSpatieTags` and
                        // Spatie's `findFromStringOfAnyType()`), so `PHP`/`php` or `Front End`/`front-end`
                        // point at the same tag. Comparing slugs catches conflicts that a raw string
                        // comparison would miss and silently detach a tag the user asked to attach.
                        $tagsToAttachSlugs = array_map(
                            static fn (string $tag): string => Str::slug($tag),
                            $get('tagsToAttach') ?? [],
                        );

                        $conflictingTags = array_filter(
                            $value ?? [],
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

                $tagIdsToAttach = $action->resolveTagsForAttaching($data['tagsToAttach'] ?? [])->pluck('id')->all();

                $tagIdsToDetach = $action->resolveTagsForDetaching($data['tagsToDetach'] ?? [])->pluck('id')->all();

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
                        if (! empty($tagIdsToAttach)) {
                            $record->tags()->syncWithoutDetaching($tagIdsToAttach);
                        }

                        if (! empty($tagIdsToDetach)) {
                            $record->tags()->detach($tagIdsToDetach);
                        }

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
