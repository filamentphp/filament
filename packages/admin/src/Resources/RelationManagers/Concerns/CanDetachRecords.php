<?php

namespace Filament\Resources\RelationManagers\Concerns;

use Filament\Facades\Filament;
use Filament\Tables;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait CanDetachRecords
{
    protected function canDetach(Model $record): bool
    {
        return $this->can('detach', $record);
    }

    protected function canDetachAny(): bool
    {
        return $this->can('detachAny');
    }

    public function detach(): void
    {
        $this->callHook('beforeDetach');

        /** @var BelongsToMany $relationship */
        $relationship = $this->getRelationship();

        $relationship->detach($this->getMountedTableActionRecord());

        $this->callHook('afterDetach');

        if (filled($this->getDetachedNotificationMessage())) {
            $this->notify('success', $this->getDetachedNotificationMessage());
        }
    }

    protected function getDetachedNotificationMessage(): ?string
    {
        return __('filament::resources/relation-managers/detach.action.messages.detached');
    }

    public function bulkDetach(): void
    {
        $this->callHook('beforeBulkDetach');

        /** @var BelongsToMany $relationship */
        $relationship = $this->getRelationship();

        $relationship->detach($this->getSelectedTableRecords());

        $this->callHook('afterBulkDetach');

        if (filled($this->getBulkDetachedNotificationMessage())) {
            $this->notify('success', $this->getBulkDetachedNotificationMessage());
        }
    }

    protected function getBulkDetachedNotificationMessage(): ?string
    {
        return __('filament::resources/relation-managers/detach.bulk_action.messages.detached');
    }

    protected function getDetachAction(): Tables\Actions\Action
    {
        return Filament::makeTableAction('detach')
            ->label(__('filament::resources/relation-managers/detach.action.label'))
            ->requiresConfirmation()
            ->modalHeading(__('filament::resources/relation-managers/detach.action.modal.heading', ['label' => static::getRecordLabel()]))
            ->action(fn () => $this->detach())
            ->color('danger')
            ->defaultIcon('heroicon-s-x')
            ->hidden(fn (Model $record): bool => ! static::canDetach($record));
    }

    protected function getDetachBulkAction(): Tables\Actions\BulkAction
    {
        return Tables\Actions\BulkAction::make('detach')
            ->label(__('filament::resources/relation-managers/detach.bulk_action.label'))
            ->action(fn () => $this->bulkDetach())
            ->requiresConfirmation()
            ->modalHeading(__('filament::resources/relation-managers/detach.bulk_action.modal.heading', ['label' => static::getPluralRecordLabel()]))
            ->deselectRecordsAfterCompletion()
            ->color('danger')
            ->icon('heroicon-o-x');
    }
}
