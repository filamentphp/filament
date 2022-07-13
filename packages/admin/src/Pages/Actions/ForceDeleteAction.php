<?php

namespace Filament\Pages\Actions;

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

        $this->color('danger');

        $this->groupedIcon('heroicon-s-trash');

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
