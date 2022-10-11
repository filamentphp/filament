<?php

namespace Filament\Resources\RelationManagers\Concerns;

use Filament\Notifications\Notification;
use Filament\Tables;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait CanDissociateRecords
{
    /**
     * @deprecated Actions are no longer pre-defined.
     */
    protected static bool $hasDissociateAction = false;

    /**
     * @deprecated Actions are no longer pre-defined.
     */
    protected static bool $hasDissociateBulkAction = false;

    /**
     * @deprecated Actions are no longer pre-defined.
     */
    protected function hasDissociateAction(): bool
    {
        return static::$hasDissociateAction;
    }

    /**
     * @deprecated Actions are no longer pre-defined.
     */
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
            Notification::make()
                ->title($this->getDissociatedNotificationMessage())
                ->success()
                ->send();
        }
    }

    /**
     * @deprecated Use `->successNotificationTitle()` on the action instead.
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
            Notification::make()
                ->title($this->getBulkDissociatedNotificationMessage())
                ->success()
                ->send();
        }
    }

    /**
     * @deprecated Use `->successNotificationTitle()` on the action instead.
     */
    protected function getBulkDissociatedNotificationMessage(): ?string
    {
        return __('filament-support::actions/dissociate.multiple.messages.dissociated');
    }

    /**
     * @deprecated Actions are no longer pre-defined.
     */
    protected function getDissociateAction(): Tables\Actions\Action
    {
        return Tables\Actions\DissociateAction::make()
            ->action(fn () => $this->dissociate());
    }

    /**
     * @deprecated Actions are no longer pre-defined.
     */
    protected function getDissociateBulkAction(): Tables\Actions\BulkAction
    {
        return Tables\Actions\DissociateBulkAction::make()
            ->action(fn () => $this->bulkDissociate());
    }
}
