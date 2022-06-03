<?php

namespace Filament\Pages\Actions;

use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Actions\Concerns\CanNotify;
use Filament\Support\Actions\Concerns\HasBeforeAfterCallbacks;

class ForceDeleteAction extends Action
{
    use CanNotify;
    use HasBeforeAfterCallbacks;

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament::resources/pages/edit-record.actions.force_delete.label'));

        $this->color('danger');

        $this->notificationMessage(__('filament::resources/pages/edit-record.actions.force_delete.messages.deleted'));

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

            $this->evaluate($this->beforeCallback, ['record' => $record]);

            $record->forceDelete();

            $this->notify();

            $this->evaluate($this->afterCallback, ['record' => $record]);

            $page->redirect($resource::getUrl('index'));
        });
    }
}
