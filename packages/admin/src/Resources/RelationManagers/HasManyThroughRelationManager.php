<?php

namespace Filament\Resources\RelationManagers;

use Filament\Resources\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

class HasManyThroughRelationManager extends HasManyRelationManager
{
    use Concerns\CanCreateRecords;
    use Concerns\CanDeleteRecords;
    use Concerns\CanEditRecords;

    protected static string $view = 'filament::resources.relation-managers.has-many-relation-manager';

    // https://github.com/laravel/framework/issues/4962
    protected function getTableQuery(): Builder
    {
        $query = parent::getTableQuery();

        return $query->select( $query->getModel()->getTable().'.*');
    }
}
