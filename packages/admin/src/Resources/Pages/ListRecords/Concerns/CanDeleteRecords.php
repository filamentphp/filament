<?php

namespace Filament\Resources\Pages\ListRecords\Concerns;

use Filament\Facades\Filament;
use Filament\Tables;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait CanDeleteRecords
{
    protected function hasDeleteAction(): bool
    {
        return true;
    }

    protected function getDeleteAction(): Tables\Actions\Action
    {
        $resource = static::getResource();

        return Filament::makeTableAction('delete')
            ->label(__('filament::resources/pages/list-records.table.actions.delete.label'))
            ->requiresConfirmation()
            ->modalHeading(fn (Model $record) => __('filament::resources/pages/list-records.table.actions.delete.modal.heading', ['label' => $resource::hasRecordTitle() ? $resource::getRecordTitle($record) : Str::title($resource::getLabel())]))
            ->action(fn () => $this->delete())
            ->color('danger')
            ->defaultIcon('heroicon-s-trash')
            ->hidden(fn (Model $record): bool => ! $resource::canDelete($record));
    }

    public function delete(): void
    {
        $this->callHook('beforeDelete');

        $this->handleRecordDeletion($this->getMountedTableActionRecord());

        $this->callHook('afterDelete');

        if (filled($this->getDeletedNotificationMessage())) {
            $this->notify('success', $this->getDeletedNotificationMessage());
        }
    }

    protected function getDeletedNotificationMessage(): ?string
    {
        return __('filament::resources/pages/list-records.table.actions.delete.messages.deleted');
    }

    protected function handleRecordDeletion(Model $record): void
    {
        $record->delete();
    }
}
