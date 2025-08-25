<?php

namespace Filament\Actions;

use Filament\Actions\Concerns\CanCustomizeProcess;
use Filament\Actions\View\ActionsIconAlias;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\TrashedFilter;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Number;
use Throwable;

class RestoreBulkAction extends BulkAction
{
    use CanCustomizeProcess;

    public static function getDefaultName(): ?string
    {
        return 'restore';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-actions::restore.multiple.label'));

        $this->modalHeading(fn (): string => __('filament-actions::restore.multiple.modal.heading', ['label' => $this->getTitleCasePluralModelLabel()]));

        $this->modalSubmitActionLabel(__('filament-actions::restore.multiple.modal.actions.restore.label'));

        $this->successNotificationTitle(__('filament-actions::restore.multiple.notifications.restored.title'));

        $this->failureNotificationTitle(function (int $successCount, int $totalCount): string {
            if ($successCount) {
                return trans_choice('filament-actions::restore.multiple.notifications.restored_partial.title', $successCount, [
                    'count' => Number::format($successCount),
                    'total' => Number::format($totalCount),
                ]);
            }

            return trans_choice('filament-actions::restore.multiple.notifications.restored_none.title', $totalCount, [
                'count' => Number::format($totalCount),
                'total' => Number::format($totalCount),
            ]);
        });

        $this->missingBulkAuthorizationFailureNotificationMessage(function (int $failureCount, int $totalCount): string {
            return trans_choice(
                ($failureCount === $totalCount)
                    ? 'filament-actions::restore.multiple.notifications.restored_none.missing_authorization_failure_message'
                    : 'filament-actions::restore.multiple.notifications.restored_partial.missing_authorization_failure_message',
                $failureCount,
                ['count' => Number::format($failureCount)],
            );
        });

        $this->missingBulkProcessingFailureNotificationMessage(function (int $failureCount, int $totalCount): string {
            return trans_choice(
                ($failureCount === $totalCount)
                    ? 'filament-actions::restore.multiple.notifications.restored_none.missing_processing_failure_message'
                    : 'filament-actions::restore.multiple.notifications.restored_partial.missing_processing_failure_message',
                $failureCount,
                ['count' => Number::format($failureCount)],
            );
        });

        $this->defaultColor('gray');

        $this->icon(FilamentIcon::resolve(ActionsIconAlias::RESTORE_ACTION) ?? Heroicon::ArrowUturnLeft);

        $this->requiresConfirmation();

        $this->modalIcon(FilamentIcon::resolve(ActionsIconAlias::RESTORE_ACTION_MODAL) ?? Heroicon::OutlinedArrowUturnLeft);

        $this->action(function (): void {
            $this->process(static function (RestoreBulkAction $action, EloquentCollection | Collection | LazyCollection $records): void {
                if (! $action->shouldFetchSelectedRecords()) {
                    try {
                        $action->reportBulkProcessingSuccessfulRecordsCount(
                            $action->getSelectedRecordsQuery()->restore(), /** @phpstan-ignore method.notFound */
                        );
                    } catch (Throwable $exception) {
                        $action->reportCompleteBulkProcessingFailure();

                        report($exception);
                    }

                    return;
                }

                $isFirstException = true;

                $records->each(static function (Model $record) use ($action, &$isFirstException): void {
                    if (! method_exists($record, 'restore')) {
                        return;
                    }

                    try {
                        $record->restore() || $action->reportBulkProcessingFailure();
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

        $this->hidden(function (HasTable $livewire): bool {
            $trashedFilterState = $livewire->getTableFilterState(TrashedFilter::class) ?? [];

            if (! array_key_exists('value', $trashedFilterState)) {
                return false;
            }

            return blank($trashedFilterState['value']);
        });
    }
}
