<?php

namespace Filament\Tables\Actions;

use Filament\Support\Actions\Concerns\CanNotify;
use Filament\Support\Actions\Concerns\HasBeforeAfterCallbacks;
use Illuminate\Database\Eloquent\Model;

class RestoreAction extends Action
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
            method_exists(($record = $this->getRecord()), 'trashed')
            && $record->trashed()
            && $this->getLivewire()::getResource()::canRestore($record)
        );

        $this->notificationMessage(__('tables::table.actions.restore.messages.restored'));

        $this->requiresConfirmation();

        $this->action(static function (RestoreAction $action, Model $record) {
            $action->evaluate($action->beforeCallback, ['record' => $record]);

            $record->restore();

            $action->notify();

            return $action->evaluate($action->afterCallback, ['record' => $record]);
        });

        parent::setUp();
    }
}
