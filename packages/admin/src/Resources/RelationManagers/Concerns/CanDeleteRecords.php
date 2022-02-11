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

        $this->notify('success', __('filament::resources/relation-managers/delete.action.messages.deleted'));
    }

    public function bulkDelete(): void
    {
        $this->callHook('beforeBulkDelete');

        $this->getSelectedTableRecords()->each(fn (Model $record) => $record->delete());

        $this->callHook('afterBulkDelete');

        $this->notify('success', __('filament::resources/relation-managers/delete.bulk_action.messages.deleted'));
    }

    protected function getDeleteAction(): Tables\Actions\Action
    {
        return config('filament.layout.tables.actions.type')::make('delete')
            ->label(__('filament::resources/relation-managers/delete.action.label'))
            ->requiresConfirmation()
            ->modalHeading(__('filament::resources/relation-managers/delete.action.modal.heading', ['label' => static::getRecordLabel()]))
            ->action(fn () => $this->delete())
            ->color('danger')
            ->icon('heroicon-o-trash')
            ->hidden(fn (Model $record): bool => ! static::canDelete($record));
    }

    protected function getDeleteBulkAction(): Tables\Actions\BulkAction
    {
        return Tables\Actions\BulkAction::make('delete')
            ->label(__('filament::resources/relation-managers/delete.bulk_action.label'))
            ->action(fn () => $this->bulkDelete())
            ->requiresConfirmation()
            ->modalHeading(__('filament::resources/relation-managers/delete.bulk_action.modal.heading', ['label' => static::getPluralRecordLabel()]))
            ->deselectRecordsAfterCompletion()
            ->color('danger')
            ->icon('heroicon-o-trash');
    }
}
