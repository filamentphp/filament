<?php

namespace Filament\Tables\Actions;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class DeleteBulkAction extends BulkAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-support::actions/delete.bulk.label'));

        $this->modalHeading(__('filament-support::actions/delete.bulk.modal.heading'));

        $this->modalButton(__('filament-support::actions/delete.bulk.modal.buttons.delete.label'));

        $this->successNotificationMessage(__('filament-support::actions/delete.bulk.messages.deleted'));

        $this->color('danger');

        $this->icon('heroicon-s-trash');

        $this->requiresConfirmation();

        $this->action(static function (DeleteBulkAction $action, Collection $records): void {
            $records->each(fn (Model $record) => $record->delete());

            $action->success();
        });

        $this->deselectRecordsAfterCompletion();
    }
}
