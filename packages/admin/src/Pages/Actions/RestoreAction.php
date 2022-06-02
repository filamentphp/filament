<?php

namespace Filament\Pages\Actions;

use Filament\Support\Actions\Concerns\CanNotify;
use Filament\Support\Actions\Concerns\HasBeforeAfterCallbacks;

class RestoreAction extends Action
{
    use CanNotify;
    use HasBeforeAfterCallbacks;

    protected function setUp(): void
    {
        $action = $this;

        $this->label(__('filament::resources/pages/edit-record.actions.restore.label'));

        $this->color('secondary');

        $this->notificationMessage(__('filament::resources/pages/edit-record.actions.restore.messages.restored'));

        $this->visible(fn () => method_exists($this->getLivewire()->record, 'trashed') && $this->getLivewire()->record->trashed());

        $this->requiresConfirmation();

        $this->modalHeading(fn () => __('filament::resources/pages/edit-record.actions.restore.modal.heading', ['label' => $this->getLivewire()::getResource()::getRecordTitle($this->getLivewire()->record)]));

        $this->modalSubheading(__('filament::resources/pages/edit-record.actions.restore.modal.subheading'));

        $this->modalButton(__('filament::resources/pages/edit-record.actions.restore.modal.buttons.restore.label'));

        $this->keyBindings(['mod+z']);

        $this->action(static function () use ($action) {
            $livewire = $action->getLivewire();
            $record = $livewire->record;

            $action->evaluate($action->beforeCallback, ['record' => $record]);

            $record->restore();

            $action->notify();

            return $action->evaluate($action->afterCallback, ['record' => $record]);
        });

        parent::setUp();
    }
}
