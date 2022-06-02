<?php

namespace Filament\Pages\Actions;

use Filament\Support\Actions\Concerns\CanNotify;
use Filament\Support\Actions\Concerns\HasBeforeAfterCallbacks;

class ForceDeleteAction extends Action
{
    use CanNotify;
    use HasBeforeAfterCallbacks;

    protected function setUp(): void
    {
        parent::setUp();

        $action = $this;

        $this->label(__('filament::resources/pages/edit-record.actions.force_delete.label'));

        $this->color('danger');

        $this->notificationMessage(__('filament::resources/pages/edit-record.actions.force_delete.messages.deleted'));

        $this->visible(fn () => method_exists($this->getLivewire()->record, 'trashed') && $this->getLivewire()->record->trashed());

        $this->requiresConfirmation();

        $this->modalHeading(fn () => __('filament::resources/pages/edit-record.actions.force_delete.modal.heading', ['label' => $this->getLivewire()::getResource()::getRecordTitle($this->getLivewire()->record)]));

        $this->modalSubheading(__('filament::resources/pages/edit-record.actions.force_delete.modal.subheading'));

        $this->modalButton(__('filament::resources/pages/edit-record.actions.force_delete.modal.buttons.delete.label'));

        $this->keyBindings(['mod+d']);

        $this->action(static function () use ($action) {
            $livewire = $action->getLivewire();
            $resource = $livewire::getResource();
            $record = $livewire->record;

            $action->evaluate($action->beforeCallback, ['record' => $record]);

            $record->forceDelete();

            $action->notify();

            $action->evaluate($action->afterCallback, ['record' => $record]);

            return $livewire->redirect($resource::getUrl('index'));
        });
    }
}
