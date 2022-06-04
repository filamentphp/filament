<?php

namespace Filament\Tables\Actions;

use Filament\Support\Actions\Concerns\CanRestoreRecords;
use Illuminate\Database\Eloquent\Collection;

class RestoreBulkAction extends BulkAction
{
    use CanRestoreRecords {
        setUp as setUpBaseTrait;
    }

    protected function setUp(): void
    {
        $this->setUpBaseTrait();

        $this->label(__('filament-support::actions/restore.bulk.label'));

        $this->successNotification(__('filament-support::actions/restore.bulk.messages.restored'));

        $this->action(static function (RestoreBulkAction $action, Collection $records): void {
            $records->each(fn ($record) => $record->restore());

            $action->sendSuccessNotification();
        });
    }
}
