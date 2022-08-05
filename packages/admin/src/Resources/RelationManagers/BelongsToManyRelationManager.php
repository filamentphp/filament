<?php

namespace Filament\Resources\RelationManagers;

use Filament\Resources\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Arr;

/**
 * @deprecated Use `RelationManager` instead, defining actions on the `$table`.
 */
class BelongsToManyRelationManager extends RelationManager
{
    use Concerns\CanAttachRecords;
    use Concerns\CanCreateRecords;
    use Concerns\CanDeleteRecords;
    use Concerns\CanDetachRecords;
    use Concerns\CanEditRecords;
    use Concerns\CanViewRecords;

    protected function getResourceTable(): Table
    {
        $table = Table::make();

        $table->actions([
            $this->getViewAction(),
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

        return $this->table($table);
    }

    /**
     * @deprecated Use `->using()` on the action instead.
     */
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

    /**
     * @deprecated Use `->using()` on the action instead.
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        /** @var BelongsToMany $relationship */
        $relationship = $this->getRelationship();

        $pivotColumns = $relationship->getPivotColumns();
        $pivotData = Arr::only($data, $pivotColumns);
        $data = Arr::except($data, $pivotColumns);

        $record->update($data);

        if (count($pivotColumns)) {
            $record->{$relationship->getPivotAccessor()}->update($pivotData);
        }

        return $record;
    }
}
