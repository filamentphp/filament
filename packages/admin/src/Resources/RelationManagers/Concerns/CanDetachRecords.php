<?php

namespace Filament\Resources\RelationManagers\Concerns;

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

        $this->getMountedTableActionRecord()->{$relationship->getPivotAccessor()}->delete();

        $this->callHook('afterDetach');

        if (filled($this->getDetachedNotificationMessage())) {
            $this->notify('success', $this->getDetachedNotificationMessage());
        }
    }

    protected function getDetachedNotificationMessage(): ?string
    {
        return __('filament-support::actions/detach.single.messages.detached');
    }

    public function bulkDetach(): void
    {
        $this->callHook('beforeBulkDetach');

        /** @var BelongsToMany $relationship */
        $relationship = $this->getRelationship();

        $this->getSelectedTableRecords()->each(
            fn (Model $recordToDetach) => $recordToDetach->{$relationship->getPivotAccessor()}->delete(),
        );

        $this->callHook('afterBulkDetach');

        if (filled($this->getBulkDetachedNotificationMessage())) {
            $this->notify('success', $this->getBulkDetachedNotificationMessage());
        }
    }

    protected function getBulkDetachedNotificationMessage(): ?string
    {
        return __('filament-support::actions/detach.multiple.messages.detached');
    }

    protected function getDetachAction(): Tables\Actions\Action
    {
        return Tables\Actions\DetachAction::make()
            ->action(fn () => $this->detach())
            ->authorize(fn (Model $record): bool => $this->canDetach($record));
    }

    protected function getDetachBulkAction(): Tables\Actions\BulkAction
    {
        return Tables\Actions\DetachBulkAction::make()
            ->action(fn () => $this->bulkDetach());
    }
}
