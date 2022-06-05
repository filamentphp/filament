<?php

namespace Filament\Tables\Actions;

class AssociateAction extends Action
{
    public static function make(string $name = 'associate'): static
    {
        return parent::make($name);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-support::actions/associate.single.label'));

        $this->modalHeading(fn (AssociateAction $action): string => __('filament-support::actions/associate.single.modal.heading', ['label' => $action->getRecordTitle()]));

        $this->modalButton(__('filament-support::actions/associate.single.modal.actions.associate.label'));

        $this->successNotificationMessage(__('filament-support::actions/associate.single.messages.associated'));

        $this->action(static function (AssociateAction $action): void {
            //

            $action->success();
        });
    }
}
