<?php

namespace Filament\Tables\Actions;

use Illuminate\Database\Eloquent\Model;

class ForceDeleteAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-support::actions/force-delete.single.label'));

        $this->modalHeading(fn (ForceDeleteAction $action): string => __('filament-support::actions/force-delete.single.modal.heading', ['label' => $action->getRecordTitle()]));

        $this->modalButton(__('filament-support::actions/force-delete.single.modal.buttons.delete.label'));

        $this->successNotificationMessage(__('filament-support::actions/force-delete.single.messages.deleted'));

        $this->color('danger');

        $this->icon('heroicon-s-trash');

        $this->requiresConfirmation();

        $this->action(static function (ForceDeleteAction $action, Model $record): void {
            $record->forceDelete();

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
