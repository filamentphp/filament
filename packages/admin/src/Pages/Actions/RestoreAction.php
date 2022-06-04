<?php

namespace Filament\Pages\Actions;

use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ViewRecord;

class RestoreAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament::resources/pages/edit-record.actions.restore.label'));

        $this->color('secondary');

        $this->successNotification(__('filament::resources/pages/edit-record.actions.restore.messages.restored'));

        $this->visible(function () {
            /** @var ViewRecord | EditRecord $page */
            $page = $this->getLivewire();

            return method_exists($page->record, 'trashed')
                && $page->record->trashed();
        });

        $this->requiresConfirmation();

        $this->modalHeading(function () {
            /** @var ViewRecord | EditRecord $page */
            $page = $this->getLivewire();

            return __(
                'filament::resources/pages/edit-record.actions.restore.modal.heading',
                ['label' => $page::getResource()::getRecordTitle($page->record)]
            );
        });

        $this->modalSubheading(__('filament::resources/pages/edit-record.actions.restore.modal.subheading'));

        $this->modalButton(__('filament::resources/pages/edit-record.actions.restore.modal.buttons.restore.label'));

        $this->keyBindings(['mod+z']);

        $this->action(function () {
            /** @var ViewRecord | EditRecord $page */
            $page = $this->getLivewire();
            $record = $page->record;

            $record->restore();

            $this->sendSuccessNotification();
        });
    }
}
