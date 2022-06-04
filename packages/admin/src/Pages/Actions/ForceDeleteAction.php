<?php

namespace Filament\Pages\Actions;

use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ViewRecord;

class ForceDeleteAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament::resources/pages/edit-record.actions.force_delete.label'));

        $this->color('danger');

        $this->successNotification(__('filament::resources/pages/edit-record.actions.force_delete.messages.deleted'));

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
                'filament::resources/pages/edit-record.actions.force_delete.modal.heading',
                ['label' => $page::getResource()::getRecordTitle($page->record)]
            );
        });

        $this->modalSubheading(__('filament::resources/pages/edit-record.actions.force_delete.modal.subheading'));

        $this->modalButton(__('filament::resources/pages/edit-record.actions.force_delete.modal.buttons.delete.label'));

        $this->keyBindings(['mod+d']);

        $this->action(function () {
            /** @var ViewRecord | EditRecord $page */
            $page = $this->getLivewire();
            $resource = $page::getResource();
            $record = $page->record;

            $record->forceDelete();

            $this->sendSuccessNotification();

            $page->redirect($resource::getUrl('index'));
        });
    }
}
