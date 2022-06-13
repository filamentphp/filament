<?php

namespace Filament\Resources\RelationManagers;

use Filament\Resources\Table;

/**
 * @deprecated Use `RelationManager` instead, defining actions on the `$table`.
 */
class HasManyRelationManager extends RelationManager
{
    use Concerns\CanAssociateRecords;
    use Concerns\CanCreateRecords;
    use Concerns\CanDeleteRecords;
    use Concerns\CanDissociateRecords;
    use Concerns\CanEditRecords;
    use Concerns\CanViewRecords;

    protected function getResourceTable(): Table
    {
        $table = Table::make();

        $table->actions([
            $this->getViewAction(),
            $this->getEditAction(),
            $this->getDissociateAction(),
            $this->getDeleteAction(),
        ]);

        $table->bulkActions(array_merge(
            ($this->canDeleteAny() ? [$this->getDeleteBulkAction()] : []),
            ($this->canDissociateAny() ? [$this->getDissociateBulkAction()] : []),
        ));

        $table->headerActions(array_merge(
            ($this->canCreate() ? [$this->getCreateAction()] : []),
            ($this->canAssociate() ? [$this->getAssociateAction()] : []),
        ));

        return $this->table($table);
    }
}
