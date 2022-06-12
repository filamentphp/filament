<?php

namespace Filament\Resources\RelationManagers\Concerns;

use Filament\Tables;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait CanDetachRecords
{
    /**
     * @deprecated Use `->action()` on the action instead.
     */
    public function detach(): void
    {
        $this->callHook('beforeDetach');

        /** @var BelongsToMany $relationship */
        $relationship = $this->getRelationship();

        $record = $this->getMountedTableActionRecord();

        if ($this->allowsDuplicates()) {
            $record->{$relationship->getPivotAccessor()}->delete();
        } else {
            $relationship->detach($record);
        }

        $this->callHook('afterDetach');

        if (filled($this->getDetachedNotificationMessage())) {
            $this->notify('success', $this->getDetachedNotificationMessage());
        }
    }

    /**
     * @deprecated Use `->successNotificationMessage()` on the action instead.
     */
    protected function getDetachedNotificationMessage(): ?string
    {
        return __('filament-support::actions/detach.single.messages.detached');
    }

    /**
     * @deprecated Use `->action()` on the action instead.
     */
    public function bulkDetach(): void
    {
        $this->callHook('beforeBulkDetach');

        /** @var BelongsToMany $relationship */
        $relationship = $this->getRelationship();

        $records = $this->getSelectedTableRecords();

        if ($this->allowsDuplicates()) {
            $records->each(
                fn (Model $recordToDetach) => $recordToDetach->{$relationship->getPivotAccessor()}->delete(),
            );
        } else {
            $relationship->detach($records);
        }

        $this->callHook('afterBulkDetach');

        if (filled($this->getBulkDetachedNotificationMessage())) {
            $this->notify('success', $this->getBulkDetachedNotificationMessage());
        }
    }

    /**
     * @deprecated Use `->successNotificationMessage()` on the action instead.
     */
    protected function getBulkDetachedNotificationMessage(): ?string
    {
        return __('filament-support::actions/detach.multiple.messages.detached');
    }

    /**
     * @deprecated Actions are no longer pre-defined.
     */
    protected function getDetachAction(): Tables\Actions\Action
    {
        return Tables\Actions\DetachAction::make()
            ->action(fn () => $this->detach());
    }

    /**
     * @deprecated Actions are no longer pre-defined.
     */
    protected function getDetachBulkAction(): Tables\Actions\BulkAction
    {
        return Tables\Actions\DetachBulkAction::make()
            ->action(fn () => $this->bulkDetach());
    }
}
