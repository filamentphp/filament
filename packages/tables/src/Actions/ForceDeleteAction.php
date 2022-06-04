<?php

namespace Filament\Tables\Actions;

use Filament\Resources\Pages\Page;

class ForceDeleteAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-support::actions.force_delete.label'));

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

        $this->successNotification(__('filament-support::actions.force_delete.messages.deleted'));

        $this->requiresConfirmation();

        $this->action(static function (ForceDeleteAction $action, $record): void {
            $record->forceDelete();

            $action->sendSuccessNotification();
        });
    }
}
