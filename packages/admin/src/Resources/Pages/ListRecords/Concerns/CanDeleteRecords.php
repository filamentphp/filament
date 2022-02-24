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

        return config('filament.layout.tables.actions.type')::make('delete')
            ->label(__('filament::resources/pages/list-records.table.actions.delete.label'))
            ->action(fn () => $this->delete())
            ->requiresConfirmation()
            ->color('danger')
            ->icon('heroicon-o-trash');
    }

    public function delete(): void
    {
        $this->callHook('beforeDelete');

        $this->handleRecordDeletion($this->getMountedTableActionRecord());

        $this->callHook('afterDelete');

        $this->notify('success', $this->getDeletedNotificationMessage());
    }

    protected function getDeletedNotificationMessage(): string
    {
        return __('filament::resources/pages/list-records.table.actions.delete.messages.deleted');
    }

    protected function handleRecordDeletion(Model $record): void
    {
        $record->delete();
    }
}
