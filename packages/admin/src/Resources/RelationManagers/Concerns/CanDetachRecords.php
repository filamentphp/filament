<?php

namespace Filament\Resources\RelationManagers\Concerns;

use Filament\Tables;
use Illuminate\Database\Eloquent\Model;

trait CanDetachRecords
{
    protected function canDetach(Model $record): bool
    {
        return $this->can('detach', $record);
    }

    protected function detach(): void
    {
        $this->callHook('beforeDetach');

        $this->getRelationship()->detach($this->getMountedTableActionRecord());

        $this->callHook('afterDetach');
    }

    protected function getDetachLinkTableAction(): Tables\Actions\LinkAction
    {
        return Tables\Actions\LinkAction::make('detach')
            ->label('Detach')
            ->requiresConfirmation()
            ->action(fn () => $this->detach())
            ->color('danger')
            ->hidden(fn (Model $record): bool => ! static::canDetach($record));
    }
}
