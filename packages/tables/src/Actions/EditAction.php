<?php

namespace Filament\Tables\Actions;

use Illuminate\Database\Eloquent\Model;

class EditAction extends Action
{
    use Concerns\InteractsWithRelationship;

    public static function make(string $name = 'edit'): static
    {
        return parent::make($name);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-support::actions/edit.single.label'));

        $this->modalHeading(fn (DeleteAction $action): string => __('filament-support::actions/edit.single.modal.heading', ['label' => $action->getRecordTitle()]));

        $this->modalButton(__('filament-support::actions/edit.single.modal.actions.edit.label'));

        $this->successNotificationMessage(__('filament-support::actions/edit.single.messages.saved'));

        $this->icon('heroicon-s-pencil');

        $this->action(static function (EditAction $action, Model $record): void {
            //

            $action->success();
        });
    }
}
