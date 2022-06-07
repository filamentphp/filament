<?php

namespace Filament\Resources\Pages\ListRecords\Concerns;

use Filament\Tables;
use Illuminate\Database\Eloquent\Model;

trait CanDeleteRecords
{
    protected function hasDeleteAction(): bool
    {
        return true;
    }

    protected function getDeleteAction(): Tables\Actions\Action
    {
        $resource = static::getResource();

        return Tables\Actions\DeleteAction::make()
            ->action(fn () => $this->delete())
            ->authorize(fn (Model $record): bool => $resource::canDelete($record));
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
            $this->notify('success', $this->getDeletedNotificationMessage());
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
