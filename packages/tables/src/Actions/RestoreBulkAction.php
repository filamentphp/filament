<?php

namespace Filament\Tables\Actions;

use Filament\Support\Actions\Concerns\CanCustomizeProcess;
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

        $this->label(__('filament-support::actions/restore.multiple.label'));

        $this->modalHeading(fn (): string => __('filament-support::actions/restore.multiple.modal.heading', ['label' => $this->getPluralModelLabel()]));

        $this->modalButton(__('filament-support::actions/restore.multiple.modal.actions.restore.label'));

        $this->successNotificationMessage(__('filament-support::actions/restore.multiple.messages.restored'));

        $this->color('secondary');

        $this->icon('heroicon-s-reply');

        $this->requiresConfirmation();

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
    }
}
