<?php

namespace Filament\Tables\Actions;

use Filament\Resources\Pages\Page;
use Filament\Support\Actions\Concerns\CanNotify;
use Filament\Support\Actions\Concerns\HasBeforeAfterCallbacks;

class ForceDeleteAction extends Action
{
    use CanNotify;
    use HasBeforeAfterCallbacks;

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('tables::table.actions.force_delete.label'));

        $this->icon('heroicon-s-trash');

        $this->color('danger');

        $this->visible(function () {
            /** @var Page $page */
            $page = $this->getLivewire();
            $record = $this->getRecord();

            return method_exists($record, 'trashed')
                && $record->trashed()
                && $page::getResource()::canForceDelete($record);
        });

        $this->notificationMessage(__('tables::table.actions.force_delete.messages.deleted'));

        $this->requiresConfirmation();

        $this->action(static function (ForceDeleteAction $action, $record) {
            $action->evaluate($action->beforeCallback, ['record' => $record]);

            $record->forceDelete();

            $action->notify();

            return $action->evaluate($action->afterCallback, ['record' => $record]);
        });
    }
}
