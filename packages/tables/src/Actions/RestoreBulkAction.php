<?php

namespace Filament\Tables\Actions;

use Filament\Support\Actions\Concerns\CanRestoreRecords;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class RestoreBulkAction extends BulkAction
{
    use CanRestoreRecords {
        setUp as setUpTrait;
    }

    protected function setUp(): void
    {
        $this->setUpTrait();

        $this->label(__('filament-support::actions/restore.bulk.label'));

        $this->modalButton(__('filament-support::actions/restore.bulk.buttons.restore.label'));

        $this->successNotification(__('filament-support::actions/restore.bulk.messages.restored'));

        $this->action(static function (RestoreBulkAction $action, Collection $records): void {
            $records->each(function (Model $record): void {
                if (! method_exists($record, 'restore')) {
                    return;
                }

                $record->restore();
            });

            $action->sendSuccessNotification();
        });

        $this->visible(true);
    }
}
