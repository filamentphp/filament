<?php

namespace Filament\Tables\Actions;

use Filament\Support\Actions\Concerns\CanCustomizeProcess;
use Illuminate\Database\Eloquent\Model;

class ForceDeleteAction extends Action
{
    use CanCustomizeProcess;

    public static function getDefaultName(): ?string
    {
        return 'forceDelete';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-support::actions/force-delete.single.label'));

        $this->modalHeading(fn (): string => __('filament-support::actions/force-delete.single.modal.heading', ['label' => $this->getRecordTitle()]));

        $this->modalButton(__('filament-support::actions/force-delete.single.modal.actions.delete.label'));

        $this->successNotificationMessage(__('filament-support::actions/force-delete.single.messages.deleted'));

        $this->color('danger');

        $this->icon('heroicon-s-trash');

        $this->requiresConfirmation();

        $this->action(function (): void {
            $this->process(static fn (Model $record) => $record->forceDelete());

            $this->success();
        });

        $this->visible(static function (Model $record): bool {
            if (! method_exists($record, 'trashed')) {
                return false;
            }

            return $record->trashed();
        });
    }
}
