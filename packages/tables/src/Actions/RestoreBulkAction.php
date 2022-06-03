<?php

namespace Filament\Tables\Actions;

use Filament\Resources\Pages\Page;
use Filament\Support\Actions\Concerns\CanNotify;
use Filament\Support\Actions\Concerns\HasBeforeAfterCallbacks;
use Illuminate\Database\Eloquent\Collection;

class RestoreBulkAction extends BulkAction
{
    use CanNotify;
    use HasBeforeAfterCallbacks;

    protected function setUp(): void
    {
        $this->label(__('tables::table.actions.restore.label'));

        $this->icon('heroicon-s-reply');

        $this->color('secondary');

        $this->visible(function () {
            /** @var Page $page */
            $page = $this->getLivewire();
            return $page::getResource()::canRestoreAny();
        });

        $this->notificationMessage(__('tables::table.bulk_actions.restore.messages.restored'));

        $this->requiresConfirmation();

        $this->action(static function (RestoreBulkAction $action, Collection $records) {
            $action->evaluate($action->beforeCallback, ['records' => $records]);

            $records->each(fn ($record) => $record->restore());

            $action->notify();

            return $action->evaluate($action->afterCallback, ['records' => $records]);
        });

        parent::setUp();
    }
}
