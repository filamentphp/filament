<?php

namespace Filament\Tables\Actions;

use Filament\Support\Actions\Concerns\CanNotify;
use Filament\Support\Actions\Concerns\HasBeforeAfterCallbacks;
use Illuminate\Database\Eloquent\Model;

class ForceDeleteAction extends Action
{
    use CanNotify;
    use HasBeforeAfterCallbacks;

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('tables::table.actions.force_delete.label'));

        $this->icon('heroicon-s-trash');

        $this->color('danger');

        $this->visible(fn () => method_exists($this->getRecord(), 'trashed') && $this->getRecord()->trashed());

        $this->notificationMessage(__('tables::table.actions.force_delete.messages.deleted'));

        $this->requiresConfirmation();

        $this->action(static function (ForceDeleteAction $action, Model $record) {
            $action->evaluate($action->beforeCallback, ['record' => $record]);

            $record->forceDelete();

            $action->notify();

            return $action->evaluate($action->afterCallback, ['record' => $record]);
        });
    }
}
