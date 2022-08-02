<?php

namespace Filament\Pages\Actions;

use Filament\Support\Actions\Concerns\CanCustomizeProcess;
use Illuminate\Database\Eloquent\Model;

class DeleteAction extends Action
{
    use CanCustomizeProcess;

    public static function getDefaultName(): ?string
    {
        return 'delete';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-support::actions/delete.single.label'));

        $this->modalHeading(fn (): string => __('filament-support::actions/delete.single.modal.heading', ['label' => $this->getRecordTitle()]));

        $this->modalButton(__('filament-support::actions/delete.single.modal.actions.delete.label'));

        $this->successNotificationMessage(__('filament-support::actions/delete.single.messages.deleted'));

        $this->color('danger');

        $this->groupedIcon('heroicon-s-trash');

        $this->requiresConfirmation();

        $this->keyBindings(['mod+d']);

        $this->hidden(static function (Model $record): bool {
            if (! method_exists($record, 'trashed')) {
                return false;
            }

            return $record->trashed();
        });

        $this->action(function (): void {
            $this->process(static fn (Model $record) => $record->delete());

            $this->success();
        });
    }
}
