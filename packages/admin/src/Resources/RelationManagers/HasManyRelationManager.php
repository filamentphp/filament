<?php

namespace Filament\Resources\RelationManagers;

use Filament\Resources\Table;

class HasManyRelationManager extends RelationManager
{
    use Concerns\CanCreateRecords;
    use Concerns\CanDeleteRecords;
    use Concerns\CanEditRecords;

    protected static string $view = 'filament::resources.relation-managers.has-many-relation-manager';

    protected function getResourceTable(): Table
    {
        if (! $this->resourceTable) {
            $table = Table::make();

            $table->actions([
                $this->getEditLinkTableAction(),
                $this->getDeleteLinkTableAction(),
            ]);

            if ($this->canDeleteAny()) {
                $table->bulkActions([$this->getDeleteTableBulkAction()]);
            }

            if ($this->canCreate()) {
                $table->headerActions([$this->getCreateButtonTableHeaderAction()]);
            }

            $this->resourceTable = static::table($table);
        }

        return $this->resourceTable;
    }
}
