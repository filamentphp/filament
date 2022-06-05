<?php

namespace Filament\Tables\Actions;

use Illuminate\Database\Eloquent\Model;

class DetachAction extends Action
{
    public static function make(string $name = 'detach'): static
    {
        return parent::make($name);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-support::actions/detach.single.label'));

        $this->modalHeading(fn (DetachAction $action): string => __('filament-support::actions/detach.single.modal.heading', ['label' => $action->getRecordTitle()]));

        $this->modalButton(__('filament-support::actions/detach.single.modal.actions.detach.label'));

        $this->successNotificationMessage(__('filament-support::actions/detach.single.messages.detached'));

        $this->color('danger');

        $this->icon('heroicon-s-x');

        $this->requiresConfirmation();

        $this->action(static function (DetachAction $action, Model $record): void {
            //

            $action->success();
        });
    }
}
