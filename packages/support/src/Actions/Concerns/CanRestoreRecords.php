<?php

namespace Filament\Support\Actions\Concerns;

use Filament\Tables\Actions\RestoreAction;
use Illuminate\Database\Eloquent\Model;

trait CanRestoreRecords
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-support::actions/restore.single.label'));

        $this->modalButton(__('filament-support::actions/restore.single.buttons.restore.label'));

        $this->successNotification(__('filament-support::actions/restore.single.messages.restored'));

        $this->color('secondary');

        $this->icon('heroicon-s-reply');

        $this->requiresConfirmation();

        $this->action(static function (RestoreAction $action, Model $record): void {
            if (! method_exists($record, 'restore')) {
                return;
            }

            $record->restore();

            $action->sendSuccessNotification();
        });

        $this->visible(static function (Model $record): bool {
            if (! method_exists($record, 'trashed')) {
                return false;
            }

            return $record->trashed();
        });
    }
}
