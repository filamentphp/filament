<?php

namespace Filament\Pages\Actions;

use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Actions\Concerns\CanNotify;
use Filament\Support\Actions\Concerns\HasBeforeAfterCallbacks;

class RestoreAction extends Action
{
    use CanNotify;
    use HasBeforeAfterCallbacks;

    protected function setUp(): void
    {
        $this->label(__('filament::resources/pages/edit-record.actions.restore.label'));

        $this->color('secondary');

        $this->notificationMessage(__('filament::resources/pages/edit-record.actions.restore.messages.restored'));

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

            $this->evaluate($this->beforeCallback, ['record' => $record]);

            $record->restore();

            $this->notify();

            return $this->evaluate($this->afterCallback, ['record' => $record]);
        });

        parent::setUp();
    }
}
