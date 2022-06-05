<?php

namespace Filament\Tables\Actions;

use Illuminate\Database\Eloquent\Model;

class RestoreAction extends Action
{
    public static function make(string $name = 'restore'): static
    {
        return parent::make($name);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-support::actions/restore.single.label'));

        $this->modalHeading(fn (RestoreAction $action): string => __('filament-support::actions/restore.single.modal.heading', ['label' => $action->getRecordTitle()]));

        $this->modalButton(__('filament-support::actions/restore.single.modal.actions.restore.label'));

        $this->successNotificationMessage(__('filament-support::actions/restore.single.messages.restored'));

        $this->color('secondary');

        $this->icon('heroicon-s-reply');

        $this->requiresConfirmation();

        $this->action(static function (\Filament\Pages\Actions\RestoreAction $action, Model $record): void {
            if (! method_exists($record, 'restore')) {
                return;
            }

            $record->restore();

            $action->success();
        });

        $this->visible(static function (Model $record): bool {
            if (! method_exists($record, 'trashed')) {
                return false;
            }

            return $record->trashed();
        });
    }
}
