<?php

namespace Filament\Resources\Pages\Concerns;

use Illuminate\Support\Facades\Gate;

trait HasRelationManagers
{
    public $activeRelationManager = null;

    protected function getRelationManagers(): array
    {
        $managers = $this->getResource()::getRelations();

        return array_filter(
            $managers,
            fn (string $manager): bool => $manager::canViewForRecord($this->record),
        );
    }
}
