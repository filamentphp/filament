<?php

namespace Filament\Tables\Actions;

use Illuminate\Database\Eloquent\Collection;

class DissociateBulkAction extends BulkAction
{
    public static function make(string $name = 'dissociate'): static
    {
        return parent::make($name);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-support::actions/dissociate.multiple.label'));

        $this->modalHeading(__('filament-support::actions/dissociate.multiple.modal.heading'));

        $this->modalButton(__('filament-support::actions/dissociate.multiple.modal.actions.dissociate.label'));

        $this->successNotificationMessage(__('filament-support::actions/dissociate.multiple.messages.dissociated'));

        $this->color('danger');

        $this->icon('heroicon-s-x');

        $this->requiresConfirmation();

        $this->action(static function (DissociateBulkAction $action, Collection $records): void {
            //

            $action->success();
        });

        $this->deselectRecordsAfterCompletion();
    }
}
