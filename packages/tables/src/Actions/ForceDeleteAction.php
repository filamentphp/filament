<?php

namespace Filament\Tables\Actions;

use Filament\Actions\Concerns\CanCustomizeProcess;
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

        $this->label(__('filament-actions::force-delete.single.label'));

        $this->modalHeading(fn (): string => __('filament-actions::force-delete.single.modal.heading', ['label' => $this->getRecordTitle()]));

        $this->modalSubmitActionLabel(__('filament-actions::force-delete.single.modal.actions.delete.label'));

        $this->successNotificationTitle(__('filament-actions::force-delete.single.notifications.deleted.title'));

        $this->color('danger');

        $this->icon('heroicon-m-trash');

        $this->requiresConfirmation();

        $this->modalIcon('heroicon-o-trash');

        $this->action(function (): void {
            try {
                $result = $this->process(static fn (Model $record) => $record->forceDelete());

                if (! $result) {
                    $this->failure();

                    return;
                }

                $this->success();
            } catch (\Illuminate\Database\QueryException $exception) {

                $this->failure();
            }
        });

        $this->visible(static function (Model $record): bool {
            if (! method_exists($record, 'trashed')) {
                return false;
            }

            return $record->trashed();
        });
    }
}
