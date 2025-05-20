<?php

namespace Filament\Tables\Actions;

use Filament\Actions\Concerns\CanCustomizeProcess;
use Filament\Support\Facades\FilamentIcon;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\TrashedFilter;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

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

        $this->modalHeading(fn (): string => __('filament-actions::restore.multiple.modal.heading', ['label' => $this->getPluralModelLabel()]));

        $this->modalSubmitActionLabel(__('filament-actions::restore.multiple.modal.actions.restore.label'));

        $this->successNotificationTitle(__('filament-actions::restore.multiple.notifications.restored.title'));

        $this->defaultColor('gray');

        $this->icon(FilamentIcon::resolve('actions::restore-action') ?? 'heroicon-m-arrow-uturn-left');

        $this->requiresConfirmation();

        $this->modalIcon(FilamentIcon::resolve('actions::restore-action.modal') ?? 'heroicon-o-arrow-uturn-left');

        $this->action(function (): void {
            $this->process(static function (Collection $records): void {
                $records->each(function (Model $record): void {
                    if (! method_exists($record, 'restore')) {
                        return;
                    }

                    $record->restore();
                });
            });

            $this->success();
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
