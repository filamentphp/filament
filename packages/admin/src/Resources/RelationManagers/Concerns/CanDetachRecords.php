<?php

namespace Filament\Resources\RelationManagers\Concerns;

use Filament\Tables;
use Illuminate\Database\Eloquent\Collection;
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
    }

    protected function getDetachTableAction(): Tables\Actions\Action
    {
        return config('filament.layout.tables.actions.type')::make('detach')
            ->label(__('filament::resources/relation-managers/detach.action.label'))
            ->requiresConfirmation()
            ->modalHeading(__('filament::resources/relation-managers/detach.action.modal.heading', ['label' => static::getRecordLabel()]))
            ->action(fn () => $this->detach())
            ->color('danger')
            ->icon('heroicon-o-x')
            ->hidden(fn (Model $record): bool => ! static::canDetach($record));
    }

    protected function getDetachTableBulkAction(): Tables\Actions\BulkAction
    {
        return Tables\Actions\BulkAction::make('detach')
            ->label(__('filament::resources/relation-managers/detach.bulk_action.label'))
            ->action(function (Collection $records) {
                /** @var BelongsToMany $relationship */
                $relationship = $this->getRelationship();

                $relationship->detach($records);
            })
            ->requiresConfirmation()
            ->modalHeading(__('filament::resources/relation-managers/detach.bulk_action.modal.heading', ['label' => static::getPluralRecordLabel()]))
            ->deselectRecordsAfterCompletion()
            ->color('danger')
            ->icon('heroicon-o-x');
    }
}
