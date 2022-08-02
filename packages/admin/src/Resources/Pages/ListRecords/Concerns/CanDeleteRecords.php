<?php

namespace Filament\Resources\Pages\ListRecords\Concerns;

use Filament\Notifications\Notification;
use Filament\Tables;
use Illuminate\Database\Eloquent\Model;

/**
 * @deprecated You may add a `DeleteAction` to the resource table.
 */
trait CanDeleteRecords
{
    /**
     * @deprecated Actions are no longer pre-defined.
     */
    protected function hasDeleteAction(): bool
    {
        return true;
    }

    /**
     * @deprecated Actions are no longer pre-defined.
     */
    protected function getDeleteAction(): Tables\Actions\Action
    {
        return Tables\Actions\DeleteAction::make()
            ->action(fn () => $this->delete());
    }

    /**
     * @deprecated Use `->action()` on the action instead.
     */
    public function delete(): void
    {
        $this->callHook('beforeDelete');

        $this->handleRecordDeletion($this->getMountedTableActionRecord());

        $this->callHook('afterDelete');

        if (filled($this->getDeletedNotificationMessage())) {
            Notification::make()
                ->title($this->getDeletedNotificationMessage())
                ->success()
                ->send();
        }
    }

    /**
     * @deprecated Use `->successNotificationMessage()` on the action instead.
     */
    protected function getDeletedNotificationMessage(): ?string
    {
        return __('filament-support::actions/delete.single.messages.deleted');
    }

    /**
     * @deprecated Use `->using()` on the action instead.
     */
    protected function handleRecordDeletion(Model $record): void
    {
        $record->delete();
    }
}
