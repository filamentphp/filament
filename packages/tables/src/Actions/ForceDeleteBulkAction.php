<?php

namespace Filament\Tables\Actions;

use Filament\Resources\Pages\Page;
use Filament\Support\Actions\Concerns\CanForceDeleteRecords;
use Filament\Support\Actions\Concerns\CanRestoreRecords;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class ForceDeleteBulkAction extends BulkAction
{
    use CanForceDeleteRecords {
        setUp as setUpTrait;
    }

    protected function setUp(): void
    {
        $this->setUpTrait();

        $this->label(__('filament-support::actions/force-delete.bulk.label'));

        $this->modalButton(__('filament-support::actions/force-delete.bulk.buttons.delete.label'));

        $this->successNotification(__('filament-support::actions/force-delete.bulk.messages.deleted'));

        $this->action(static function (ForceDeleteBulkAction $action, Collection $records): void {
            $records->each(fn (Model $record) => $record->forceDelete());

            $action->sendSuccessNotification();
        });

        $this->visible(true);
    }
}
