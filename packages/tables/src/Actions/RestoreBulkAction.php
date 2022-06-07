<?php

namespace Filament\Tables\Actions;

use Filament\Support\Actions\Concerns\CanCustomizeProcess;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class RestoreBulkAction extends BulkAction
{
    use CanCustomizeProcess;

    public static function make(string $name = 'restore'): static
    {
        return parent::make($name);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-support::actions/restore.multiple.label'));

        $this->modalHeading(__('filament-support::actions/restore.multiple.modal.heading'));

        $this->modalButton(__('filament-support::actions/restore.multiple.modal.actions.restore.label'));

        $this->successNotificationMessage(__('filament-support::actions/restore.multiple.messages.restored'));

        $this->color('secondary');

        $this->icon('heroicon-s-reply');

        $this->requiresConfirmation();

        $this->action(static function (RestoreBulkAction $action): void {
            $action->process(static function (Collection $records): void {
                $records->each(function (Model $record): void {
                    if (! method_exists($record, 'restore')) {
                        return;
                    }

                    $record->restore();
                });
            });

            $action->success();
        });

        $this->deselectRecordsAfterCompletion();
    }
}
