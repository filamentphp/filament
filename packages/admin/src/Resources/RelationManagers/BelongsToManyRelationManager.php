<?php

namespace Filament\Resources\RelationManagers;

use Filament\Resources\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;

class BelongsToManyRelationManager extends RelationManager
{
    use Concerns\CanAttachRecords;
    use Concerns\CanCreateRecords;
    use Concerns\CanDeleteRecords;
    use Concerns\CanDetachRecords;
    use Concerns\CanEditRecords;

    protected bool $allowsDuplicates = false;

    protected static string $view = 'filament::resources.relation-managers.belongs-to-many-relation-manager';

    public function allowsDuplicates(): bool
    {
        return $this->allowsDuplicates;
    }

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
            if ($this->allowsDuplicates()) {
                $record->{$relationship->getPivotAccessor()}->update($pivotData);
            } else {
                $relationship->updateExistingPivot($record, $pivotData);
            }
        }

        return $record;
    }

    protected function getTableQuery(): Builder
    {
        $query = parent::getTableQuery();

        /** @var BelongsToMany $relationship */
        $relationship = $this->getRelationship();

        // https://github.com/laravel/framework/issues/4962
        if (! $this->allowsDuplicates()) {
            $this->selectPivotDataInQuery($query);
        }

        // https://github.com/laravel-filament/filament/issues/2079
        $query->withCasts(
            app($relationship->getPivotClass())->getCasts(),
        );

        return $query;
    }

    protected function resolveTableRecord(?string $key): ?Model
    {
        if (! $this->allowsDuplicates()) {
            return parent::resolveTableRecord($key);
        }

        /** @var BelongsToMany $relationship */
        $relationship = $this->getRelationship();

        $pivotClass = $relationship->getPivotClass();
        $pivotKeyName = app($pivotClass)->getKeyName();

        $record = $this->selectPivotDataInQuery(
            $relationship->wherePivot($pivotKeyName, $key),
        )->first();

        return $record?->setRawAttributes($record->getRawOriginal());
    }

    public function getSelectedTableRecords(): Collection
    {
        if (! $this->allowsDuplicates()) {
            return parent::getSelectedTableRecords();
        }

        /** @var BelongsToMany $relationship */
        $relationship = $this->getRelationship();

        $pivotClass = $relationship->getPivotClass();
        $pivotKeyName = app($pivotClass)->getKeyName();

        return $this->selectPivotDataInQuery(
            $relationship->wherePivotIn($pivotKeyName, $this->selectedTableRecords),
        )->get();
    }

    protected function selectPivotDataInQuery(Builder | Relation $query): Builder | Relation
    {
        /** @var BelongsToMany $relationship */
        $relationship = $this->getRelationship();

        return $query->select(
            $relationship->getTable().'.*',
            $query->getModel()->getTable().'.*',
        );
    }

    public function getTableRecordKey(Model $record): string
    {
        if (! $this->allowsDuplicates()) {
            return parent::getTableRecordKey($record);
        }

        /** @var BelongsToMany $relationship */
        $relationship = $this->getRelationship();

        $pivotClass = $relationship->getPivotClass();
        $pivotKeyName = app($pivotClass)->getKeyName();

        return $record->getAttributeValue($pivotKeyName);
    }
}
