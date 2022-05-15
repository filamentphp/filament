<?php

namespace Filament\Resources\RelationManagers;

use Illuminate\Database\Eloquent\Builder;

class HasManyThroughRelationManager extends HasManyRelationManager
{
    protected function getTableQuery(): Builder
    {
        $query = parent::getTableQuery();

        // https://github.com/laravel/framework/issues/4962
        $query->select($query->getModel()->getTable().'.*');

        return $query;
    }
}
