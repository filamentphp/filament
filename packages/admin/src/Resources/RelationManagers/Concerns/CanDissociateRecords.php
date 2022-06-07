<?php

namespace Filament\Resources\RelationManagers\Concerns;

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

    /**
     * @deprecated Use `->action()` on the action instead.
     */
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

    /**
     * @deprecated Use `->successNotificationMessage()` on the action instead.
     */
    protected function getDissociatedNotificationMessage(): ?string
    {
        return __('filament-support::actions/dissociate.single.messages.dissociated');
    }

    /**
     * @deprecated Use `->action()` on the action instead.
     */
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

    /**
     * @deprecated Use `->successNotificationMessage()` on the action instead.
     */
    protected function getBulkDissociatedNotificationMessage(): ?string
    {
        return __('filament-support::actions/dissociate.multiple.messages.dissociated');
    }

    protected function getDissociateAction(): Tables\Actions\Action
    {
        return Tables\Actions\DissociateAction::make()
            ->action(fn () => $this->dissociate())
            ->authorize(fn (Model $record): bool => $this->canDissociate($record));
    }

    protected function getDissociateBulkAction(): Tables\Actions\BulkAction
    {
        return Tables\Actions\DissociateBulkAction::make()
            ->action(fn () => $this->bulkDissociate());
    }
}
