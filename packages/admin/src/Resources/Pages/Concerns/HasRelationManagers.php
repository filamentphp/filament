<?php

namespace Filament\Resources\Pages\Concerns;

use Illuminate\Support\Arr;

trait HasRelationManagers
{
    public $activeRelationManager = null;

    protected function getRelationManagers(): array
    {
        $managers = $this->getResource()::getRelations();
        if(!Arr::isAssoc($managers)) {
            $managers = array_combine($managers, $managers);
        }
        
        return array_filter(
            $managers,
            fn (string $manager): bool => $manager::canViewForRecord($this->record),
        );
    }

    public function mountHasRelationManagers(): void
    {
        $managers = $this->getRelationManagers();

        if (array_key_exists($this->activeRelationManager, $managers)) {
            return;
        }

        $this->activeRelationManager = array_key_first($this->getRelationManagers()) ?? null;
    }
}
