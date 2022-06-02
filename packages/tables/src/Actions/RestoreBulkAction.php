<?php

namespace Filament\Tables\Actions;

use Filament\Support\Actions\Concerns\CanNotify;
use Filament\Support\Actions\Concerns\HasBeforeAfterCallbacks;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class RestoreBulkAction extends BulkAction
{
    use CanNotify;
    use HasBeforeAfterCallbacks;

    protected function setUp(): void
    {
        $this->label(__('tables::table.actions.restore.label'));

        $this->icon('heroicon-s-reply');

        $this->color('secondary');

        $this->visible(
            fn () =>
            $this->getLivewire()::getResource()::canRestoreAny()
        );

        $this->notificationMessage(__('tables::table.bulk_actions.restore.messages.restored'));

        $this->requiresConfirmation();

        $this->action(static function (RestoreBulkAction $action, Collection $records) {
            $action->evaluate($action->beforeCallback, ['records' => $records]);

            $records->each(fn (Model $record) => $record->restore());

            $action->notify();

            return $action->evaluate($action->afterCallback, ['records' => $records]);
        });

        parent::setUp();
    }
}
