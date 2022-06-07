<?php

namespace Filament\Tables\Actions;

use Filament\Support\Actions\Concerns\CanCustomizeProcess;
use Illuminate\Database\Eloquent\Model;

class RestoreAction extends Action
{
    use CanCustomizeProcess;

    public static function make(string $name = 'restore'): static
    {
        return parent::make($name);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-support::actions/restore.single.label'));

        $this->modalHeading(fn (RestoreAction $action): string => __('filament-support::actions/restore.single.modal.heading', ['label' => $action->getRecordTitle()]));

        $this->modalButton(__('filament-support::actions/restore.single.modal.actions.restore.label'));

        $this->successNotificationMessage(__('filament-support::actions/restore.single.messages.restored'));

        $this->color('secondary');

        $this->icon('heroicon-s-reply');

        $this->requiresConfirmation();

        $this->action(static function (RestoreAction $action): void {
            $action->process(static function (Model $record) use ($action): void {
                if (! method_exists($record, 'restore')) {
                    $action->failure();

                    return;
                }

                $record->restore();
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
