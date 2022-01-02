<?php

namespace Filament\Resources\Pages\Concerns;

trait HasRelationManagers
{
    public $activeRelationManager = null;

    protected function getRelationManagers(): array
    {
        return $this->getResource()::getRelations();
    }
}
