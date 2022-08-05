<?php

namespace Filament\Tables\Actions;

use Filament\Support\Actions\Concerns\CanCustomizeProcess;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class ForceDeleteBulkAction extends BulkAction
{
    use CanCustomizeProcess;

    public static function getDefaultName(): ?string
    {
        return 'forceDelete';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-support::actions/force-delete.multiple.label'));

        $this->modalHeading(fn (): string => __('filament-support::actions/force-delete.multiple.modal.heading', ['label' => $this->getPluralModelLabel()]));

        $this->modalButton(__('filament-support::actions/force-delete.multiple.modal.actions.delete.label'));

        $this->successNotificationMessage(__('filament-support::actions/force-delete.multiple.messages.deleted'));

        $this->color('danger');

        $this->icon('heroicon-s-trash');

        $this->requiresConfirmation();

        $this->action(function (): void {
            $this->process(static function (Collection $records): void {
                $records->each(fn (Model $record) => $record->forceDelete());
            });

            $this->success();
        });

        $this->deselectRecordsAfterCompletion();
    }
}
