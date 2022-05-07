<?php

namespace Filament\Resources\RelationManagers;

use Filament\Resources\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Arr;

class BelongsToManyRelationManager extends RelationManager
{
    use Concerns\CanAttachRecords;
    use Concerns\CanCreateRecords;
    use Concerns\CanDeleteRecords;
    use Concerns\CanDetachRecords;
    use Concerns\CanEditRecords;

    protected static string $view = 'filament::resources.relation-managers.belongs-to-many-relation-manager';

    protected function getResourceTable(): Table
    {
        if (! $this->resourceTable) {
            $table = Table::make();

            $table->actions([
                $this->getEditAction(),
                $this->getDetachAction(),
                $this->getDeleteAction(),
            ]);

            $table->bulkActions(array_merge(
                ($this->canDeleteAny() ? [$this->getDeleteBulkAction()] : []),
                ($this->canDetachAny() ? [$this->getDetachBulkAction()] : []),
            ));

            $table->headerActions(array_merge(
                ($this->canCreate() ? [$this->getCreateAction()] : []),
                ($this->canAttach() ? [$this->getAttachAction()] : []),
            ));

            $this->resourceTable = static::table($table);
        }

        return $this->resourceTable;
    }

    protected function handleRecordCreation(array $data): Model
    {
        /** @var BelongsToMany $relationship */
        $relationship = $this->getRelationship();

        $pivotColumns = $relationship->getPivotColumns();
        $pivotData = Arr::only($data, $pivotColumns);
        $data = Arr::except($data, $pivotColumns);

        $record = $relationship->getQuery()->create($data);
        $this->getMountedTableActionForm()->model($record)->saveRelationships();
        $relationship->attach($record, $pivotData);

        return $record;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        /** @var BelongsToMany $relationship */
        $relationship = $this->getRelationship();

        $pivotColumns = $relationship->getPivotColumns();
        $pivotData = Arr::only($data, $pivotColumns);
        $data = Arr::except($data, $pivotColumns);

        $record->update($data);

        if (count($pivotColumns)) {
            $relationship->updateExistingPivot($record, $pivotData);
        }

        return $record;
    }

    protected function getTableQuery(): Builder
    {
        $query = parent::getTableQuery();

        /** @var BelongsToMany $relationship */
        $relationship = $this->getRelationship();

        // https://github.com/laravel/framework/issues/4962
        $query->select(
            $relationship->getTable().'.*',
            $query->getModel()->getTable().'.*',
        );

        // https://github.com/laravel-filament/filament/issues/2079
        $query->withCasts(
            app($relationship->getPivotClass())->getCasts(),
        );

        return $query;
    }
}
