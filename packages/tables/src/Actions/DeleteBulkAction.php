<?php

namespace Filament\Tables\Actions;

use Filament\Support\Actions\Concerns\CanCustomizeProcess;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class DeleteBulkAction extends BulkAction
{
    use CanCustomizeProcess;

    public static function make(string $name = 'delete'): static
    {
        return parent::make($name);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-support::actions/delete.multiple.label'));

        $this->modalHeading(__('filament-support::actions/delete.multiple.modal.heading'));

        $this->modalButton(__('filament-support::actions/delete.multiple.modal.actions.delete.label'));

        $this->successNotificationMessage(__('filament-support::actions/delete.multiple.messages.deleted'));

        $this->color('danger');

        $this->icon('heroicon-s-trash');

        $this->requiresConfirmation();

        $this->action(static function (DeleteBulkAction $action): void {
            $action->process(static function (Collection $records): void {
                $records->each(fn (Model $record) => $record->delete());
            });

            $action->success();
        });

        $this->deselectRecordsAfterCompletion();
    }
}
