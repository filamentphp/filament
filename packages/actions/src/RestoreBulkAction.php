<?php

namespace Filament\Actions;

use Filament\Actions\Concerns\CanCustomizeProcess;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\TrashedFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Number;

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

        $this->failureNotificationMissingMessage(function (int $missingMessageCount, int $successCount): string {
            return trans_choice(
                $successCount
                    ? 'filament-actions::restore.multiple.notifications.restored_partial.missing_message'
                    : 'filament-actions::restore.multiple.notifications.restored_none.missing_message',
                $missingMessageCount,
                ['count' => Number::format($missingMessageCount)],
            );
        });

        $this->color('gray');

        $this->icon(FilamentIcon::resolve('actions::restore-action') ?? Heroicon::ArrowUturnLeft);

        $this->requiresConfirmation();

        $this->modalIcon(FilamentIcon::resolve('actions::restore-action.modal') ?? Heroicon::OutlinedArrowUturnLeft);

        $this->action(fn () => $this->processIndividualRecords(
            static function (Model $record): void {
                if (! method_exists($record, 'restore')) {
                    return;
                }

                $record->restore();
            },
        ));

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
