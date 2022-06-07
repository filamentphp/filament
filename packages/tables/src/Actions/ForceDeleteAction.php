<?php

namespace Filament\Tables\Actions;

use Filament\Support\Actions\Concerns\CanCustomizeProcess;
use Illuminate\Database\Eloquent\Model;

class ForceDeleteAction extends Action
{
    use CanCustomizeProcess;

    public static function make(string $name = 'forceDelete'): static
    {
        return parent::make($name);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-support::actions/force-delete.single.label'));

        $this->modalHeading(fn (ForceDeleteAction $action): string => __('filament-support::actions/force-delete.single.modal.heading', ['label' => $action->getRecordTitle()]));

        $this->modalButton(__('filament-support::actions/force-delete.single.modal.actions.delete.label'));

        $this->successNotificationMessage(__('filament-support::actions/force-delete.single.messages.deleted'));

        $this->color('danger');

        $this->icon('heroicon-s-trash');

        $this->requiresConfirmation();

        $this->action(static function (ForceDeleteAction $action): void {
            $action->process(static function (Model $record): void {
                $record->forceDelete();
            });

            $action->success();
        });

        $this->visible(static function (Model $record): bool {
            if (! method_exists($record, 'trashed')) {
                return false;
            }

            return $record->trashed();
        });
    }
}
