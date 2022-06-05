<?php

namespace Filament\Tables\Actions;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class RestoreBulkAction extends BulkAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-support::actions/restore.bulk.label'));

        $this->modalHeading(__('filament-support::actions/restore.bulk.modal.heading'));

        $this->modalButton(__('filament-support::actions/restore.bulk.modal.buttons.restore.label'));

        $this->successNotificationMessage(__('filament-support::actions/restore.bulk.messages.restored'));

        $this->color('secondary');

        $this->icon('heroicon-s-reply');

        $this->requiresConfirmation();

        $this->action(static function (RestoreBulkAction $action, Collection $records): void {
            $records->each(function (Model $record): void {
                if (! method_exists($record, 'restore')) {
                    return;
                }

                $record->restore();
            });

            $action->success();
        });

        $this->deselectRecordsAfterCompletion();
    }
}
