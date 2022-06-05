<?php

namespace Filament\Tables\Actions;

class AttachAction extends Action
{
    public static function make(string $name = 'attach'): static
    {
        return parent::make($name);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-support::actions/attach.single.label'));

        $this->modalHeading(fn (AttachAction $action): string => __('filament-support::actions/attach.single.modal.heading', ['label' => $action->getRecordTitle()]));

        $this->modalButton(__('filament-support::actions/attach.single.modal.actions.attach.label'));

        $this->successNotificationMessage(__('filament-support::actions/attach.single.messages.attached'));

        $this->action(static function (AttachAction $action): void {
            //

            $action->success();
        });
    }
}
