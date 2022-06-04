<?php

namespace Filament\Support\Actions\Concerns;

use Filament\Tables\Actions\ForceDeleteAction;
use Illuminate\Database\Eloquent\Model;

trait CanDeleteRecords
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-support::actions/delete.single.label'));

        $this->modalButton(__('filament-support::actions/delete.single.buttons.delete.label'));

        $this->successNotification(__('filament-support::actions/delete.single.messages.deleted'));

        $this->color('danger');

        $this->icon('heroicon-s-trash');

        $this->requiresConfirmation();

        $this->action(static function (ForceDeleteAction $action, Model $record): void {
            $record->delete();

            $action->sendSuccessNotification();
        });

        $this->hidden(static function (Model $record): bool {
            if (! method_exists($record, 'trashed')) {
                return true;
            }

            return $record->trashed();
        });
    }
}
