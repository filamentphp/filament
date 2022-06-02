<?php

namespace Filament\Tables\Actions;

use Filament\Support\Actions\Concerns\CanNotify;
use Filament\Support\Actions\Concerns\HasBeforeAfterCallbacks;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class ForceDeleteBulkAction extends BulkAction
{
    use CanNotify;
    use HasBeforeAfterCallbacks;

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('tables::table.actions.force_delete.label'));

        $this->icon('heroicon-s-trash');

        $this->color('danger');

        $this->visible(fn () =>
            $this->getLivewire()::getResource()::canForceDeleteAny()
        );

        $this->notificationMessage(__('tables::table.bulk_actions.force_delete.messages.deleted'));

        $this->requiresConfirmation();

        $this->action(static function (ForceDeleteBulkAction $action, Collection $records) {
            $action->evaluate($action->beforeCallback, ['records' => $records]);

            $records->each(fn (Model $record) => $record->forceDelete());

            $action->notify();

            return $action->evaluate($action->afterCallback, ['records' => $records]);
        });
    }
}
