<?php

namespace Filament\Pages\Actions;

use Filament\Resources\Pages\EditRecord;
use Filament\Support\Actions\Concerns\CanNotify;
use Filament\Support\Actions\Concerns\CanRedirect;
use Filament\Support\Actions\Concerns\HasBeforeAfterCallbacks;
use Illuminate\Database\Eloquent\Model;

use function Livewire\invade;

class DeleteAction extends Action
{
    use CanNotify;
    use CanRedirect;
    use HasBeforeAfterCallbacks;

    protected function setUp(): void
    {
        $this->label(__('filament::resources/pages/edit-record.actions.delete.label'));

        $this->requiresConfirmation();

        $this->modalHeading(
            fn () => __('filament::resources/pages/edit-record.actions.delete.modal.heading', ['label' => invade($this->getLivewire())->getRecordTitle() ?? $this->getLivewire()::getResource()::getLabel()])
        );

        $this->modalSubheading(__('filament::resources/pages/edit-record.actions.delete.modal.subheading'));

        $this->modalButton(__('filament::resources/pages/edit-record.actions.delete.modal.buttons.delete.label'));

        $this->action(fn () => $this->execute());

        $this->keyBindings(['mod+d']);

        $this->color('danger');

        // Compatibility with V2

        $this->notificationMessage(fn () => invade($this->getLivewire())->getDeletedNotificationMessage());

        $this->redirectUrl(fn () => invade($this->getLivewire())->getDeleteRedirectUrl());

        $this->callBefore(fn () => invade($this->getLivewire())->callHook('beforeDelete'));

        $this->callAfter(fn () => invade($this->getLivewire())->callHook('afterDelete'));
    }

    public function getRecord(): ?Model
    {
        return $this->getLivewire()->record;
    }

    public function execute()
    {
        $page = $this->getLivewire();

        $reflector = new \ReflectionMethod($page, 'delete');

        if ($reflector->getDeclaringClass()->getName() !== EditRecord::class) {
            // Compatiblity for V2
            return $page->delete();
        }

        // New implementation

        $record = $this->getRecord();

        abort_unless($page::getResource()::canDelete($record), 403);

        $this->evaluate($this->beforeCallback, ['record' => $record]);

        $record->delete();

        $this->evaluate($this->afterCallback, ['record' => $record]);

        $this->notify();

        $this->redirect();
    }
}
