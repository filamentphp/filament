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

        // @TODO: Set translation
        // $this->label(__('tables::pages.actions.force_delete.label'));
        $this->label('FORCE DELETE');

        $this->color('danger');

        // $this->modalButton(static fn (ForceDeleteAction $action): string => $action->getLabel());

        // @TODO: Set translation
        // $this->notificationMessage(__('tables::pages.actions.force_delete.messages.deleted'));
        $this->notificationMessage('DELETED');

        $this->visible(fn () => method_exists($this->getLivewire()->record, 'trashed') && $this->getLivewire()->record->trashed());

        $this->requiresConfirmation();

        $this->modalHeading(fn () => __('filament::resources/pages/edit-record.actions.delete.modal.heading', ['label' => 'BLA']));
        $this->modalSubheading(__('filament::resources/pages/edit-record.actions.delete.modal.subheading'));
        $this->modalButton(__('filament::resources/pages/edit-record.actions.delete.modal.buttons.delete.label'));
        $this->keyBindings(['mod+d']);

        $this->action(static function () use ($action) {

            $record = $action->getLivewire()->record;

            $action->evaluate($action->beforeCallback, ['record' => $record]);

            $record->forceDelete();

            $action->notify();

            $action->evaluate($action->afterCallback, ['record' => $record]);
        });
    }

}
