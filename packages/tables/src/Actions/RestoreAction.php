<?php

namespace Filament\Tables\Actions;

use Filament\Resources\Pages\Page;
use Filament\Support\Actions\Concerns\CanNotify;
use Filament\Support\Actions\Concerns\HasBeforeAfterCallbacks;

class RestoreAction extends Action
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
            $record = $this->getRecord();

            return method_exists($record, 'trashed')
                && $record->trashed()
                && $page::getResource()::canRestore($record);
        });

        $this->notificationMessage(__('tables::table.actions.restore.messages.restored'));

        $this->requiresConfirmation();

        $this->action(static function (RestoreAction $action, $record) {
            $action->evaluate($action->beforeCallback, ['record' => $record]);

            $record->restore();

            $action->notify();

            return $action->evaluate($action->afterCallback, ['record' => $record]);
        });

        parent::setUp();
    }
}
