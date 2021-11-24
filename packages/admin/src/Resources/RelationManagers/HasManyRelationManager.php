<?php

namespace Filament\Resources\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;

class HasManyRelationManager extends RelationManager
{
    public $createData;

    public $editData;

    protected static string $view = 'filament::resources.relation-managers.has-many-relation-manager';

    protected function getForms(): array
    {
        return array_merge($this->getTableForms(), [
            'createForm' => $this->makeForm()
                ->schema($this->getFormSchema())
                ->statePath('createData'),
            'editForm' => $this->makeForm()
                ->schema($this->getFormSchema())
                ->statePath('editData'),
        ]);
    }

    protected function getTableQuery(): Builder
    {
        return $this->getRelationship()->getQuery();
    }
}
