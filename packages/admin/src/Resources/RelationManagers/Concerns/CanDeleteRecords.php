<?php

namespace Filament\Resources\RelationManagers\Concerns;

use Filament\Tables;
use Illuminate\Database\Eloquent\Model;

trait CanDeleteRecords
{
    protected function canDelete(Model $record): bool
    {
        return $this->can('delete', $record);
    }

    protected function canDeleteAny(): bool
    {
        return $this->can('deleteAny');
    }

    public function delete(): void
    {
        $this->callHook('beforeDelete');

        $this->getMountedTableActionRecord()->delete();

        $this->callHook('afterDelete');

        if (filled($this->getDeletedNotificationMessage())) {
            $this->notify('success', $this->getDeletedNotificationMessage());
        }
    }

    protected function getDeletedNotificationMessage(): ?string
    {
        return __('filament-support::actions/delete.single.messages.deleted');
    }

    public function bulkDelete(): void
    {
        $this->callHook('beforeBulkDelete');

        $this->getSelectedTableRecords()->each(fn (Model $record) => $record->delete());

        $this->callHook('afterBulkDelete');

        if (filled($this->getBulkDeletedNotificationMessage())) {
            $this->notify('success', $this->getBulkDeletedNotificationMessage());
        }
    }

    protected function getBulkDeletedNotificationMessage(): ?string
    {
        return __('filament-support::actions/delete.multiple.messages.deleted');
    }

    protected function getDeleteAction(): Tables\Actions\Action
    {
        return Tables\Actions\DeleteAction::make()
            ->action(fn () => $this->delete())
            ->authorize(fn (Model $record): bool => $this->canDelete($record));
    }

    protected function getDeleteBulkAction(): Tables\Actions\BulkAction
    {
        return Tables\Actions\DeleteBulkAction::make()
            ->action(fn () => $this->bulkDelete());
    }
}
