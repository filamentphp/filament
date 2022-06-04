<?php

namespace Filament\Tables\Actions;

use Filament\Support\Actions\Concerns\CanDeleteRecords;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class DeleteBulkAction extends BulkAction
{
    use CanDeleteRecords {
        setUp as setUpTrait;
    }

    protected function setUp(): void
    {
        $this->setUpTrait();

        $this->label(__('filament-support::actions/delete.bulk.label'));

        $this->modalButton(__('filament-support::actions/delete.bulk.buttons.delete.label'));

        $this->successNotification(__('filament-support::actions/delete.bulk.messages.deleted'));

        $this->action(static function (DeleteBulkAction $action, Collection $records): void {
            $records->each(fn (Model $record) => $record->delete());

            $action->sendSuccessNotification();
        });

        $this->hidden(false);
    }
}
