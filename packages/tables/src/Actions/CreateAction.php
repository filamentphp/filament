<?php

namespace Filament\Tables\Actions;

use Illuminate\Database\Eloquent\Model;

class CreateAction extends Action
{
    public static function make(string $name = 'create'): static
    {
        return parent::make($name);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-support::actions/create.single.label'));

        $this->modalHeading(fn (CreateAction $action): string => __('filament-support::actions/create.single.modal.heading', ['label' => $action->getRecordTitle()]));

        $this->modalButton(__('filament-support::actions/create.single.modal.actions.create.label'));

        $this->successNotificationMessage(__('filament-support::actions/create.single.messages.created'));

        $this->action(static function (CreateAction $action): void {
            //

            $action->success();
        });
    }
}
