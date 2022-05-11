<?php

namespace Filament\Resources\RelationManagers\Concerns;

use Filament\Facades\Filament;
use Filament\Tables;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait CanDissociateRecords
{
    protected static bool $hasDissociateAction = false;

    protected static bool $hasDissociateBulkAction = false;

    protected function hasDissociateAction(): bool
    {
        return static::$hasDissociateAction;
    }

    protected function hasDissociateBulkAction(): bool
    {
        return static::$hasDissociateBulkAction;
    }

    protected function canDissociate(Model $record): bool
    {
        return $this->hasDissociateAction() && $this->can('dissociate', $record);
    }

    protected function canDissociateAny(): bool
    {
        return $this->hasDissociateBulkAction() && $this->can('dissociateAny');
    }

    public function dissociate(): void
    {
        $this->callHook('beforeDissociate');

        $recordToDissociate = $this->getMountedTableActionRecord();

        /** @var BelongsTo $inverseRelationship */
        $inverseRelationship = $this->getInverseRelationshipFor($recordToDissociate);

        $inverseRelationship->dissociate();
        $recordToDissociate->save();

        $this->callHook('afterDissociate');

        if (filled($this->getDissociatedNotificationMessage())) {
            $this->notify('success', $this->getDissociatedNotificationMessage());
        }
    }

    protected function getDissociatedNotificationMessage(): ?string
    {
        return __('filament::resources/relation-managers/dissociate.action.messages.dissociated');
    }

    public function bulkDissociate(): void
    {
        $this->callHook('beforeBulkDissociate');

        $this->getSelectedTableRecords()->each(function (Model $recordToDissociate): void {
            /** @var BelongsTo $inverseRelationship */
            $inverseRelationship = $this->getInverseRelationshipFor($recordToDissociate);

            $inverseRelationship->dissociate();
            $recordToDissociate->save();
        });

        $this->callHook('afterBulkDissociate');

        if (filled($this->getBulkDissociatedNotificationMessage())) {
            $this->notify('success', $this->getBulkDissociatedNotificationMessage());
        }
    }

    protected function getBulkDissociatedNotificationMessage(): ?string
    {
        return __('filament::resources/relation-managers/dissociate.bulk_action.messages.dissociated');
    }

    protected function getDissociateAction(): Tables\Actions\Action
    {
        return Filament::makeTableAction('dissociate')
            ->label(__('filament::resources/relation-managers/dissociate.action.label'))
            ->requiresConfirmation()
            ->modalHeading(__('filament::resources/relation-managers/dissociate.action.modal.heading', ['label' => static::getRecordLabel()]))
            ->action(fn () => $this->dissociate())
            ->color('danger')
            ->icon('heroicon-s-x')
            ->hidden(fn (Model $record): bool => ! static::canDissociate($record));
    }

    protected function getDissociateBulkAction(): Tables\Actions\BulkAction
    {
        return Tables\Actions\BulkAction::make('dissociate')
            ->label(__('filament::resources/relation-managers/dissociate.bulk_action.label'))
            ->action(fn () => $this->bulkDissociate())
            ->requiresConfirmation()
            ->modalHeading(__('filament::resources/relation-managers/dissociate.bulk_action.modal.heading', ['label' => static::getPluralRecordLabel()]))
            ->deselectRecordsAfterCompletion()
            ->color('danger')
            ->icon('heroicon-o-x');
    }
}
