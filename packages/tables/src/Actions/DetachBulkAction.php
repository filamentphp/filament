<?php

namespace Filament\Tables\Actions;

use Filament\Support\Actions\Concerns\CanDetachRecords;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class DetachBulkAction extends BulkAction
{
    public static function make(string $name = 'detach'): static
    {
        return parent::make($name);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-support::actions/detach.multiple.label'));

        $this->modalHeading(__('filament-support::actions/detach.multiple.modal.heading'));

        $this->modalButton(__('filament-support::actions/detach.multiple.modal.actions.detach.label'));

        $this->successNotificationMessage(__('filament-support::actions/detach.multiple.messages.detached'));

        $this->color('danger');

        $this->icon('heroicon-s-x');

        $this->requiresConfirmation();

        $this->action(static function (DetachBulkAction $action, Collection $records): void {
            //

            $action->success();
        });

        $this->deselectRecordsAfterCompletion();
    }
}
