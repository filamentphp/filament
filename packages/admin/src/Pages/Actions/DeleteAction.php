<?php

namespace Filament\Pages\Actions;

use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ViewRecord;
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

        $this->modalHeading(function () {
            $page = invade($this->getLivewire());

            return __('filament::resources/pages/edit-record.actions.delete.modal.heading', [
                'label' => $page->getRecordTitle() ?? $page::getResource()::getLabel()
            ]);
        });

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
        /** @var ViewRecord | EditRecord $page */
        $page = $this->getLivewire();

        return $page->record;
    }

    public function execute()
    {
        /** @var ViewRecord | EditRecord $page */
        $page = $this->getLivewire();

        // Compatiblity for V2

        $reflector = new \ReflectionMethod($page, 'delete');

        if ($reflector->getDeclaringClass()->getName() !== EditRecord::class) {
            $page->delete();
            return;
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
