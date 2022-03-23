<?php

namespace Filament\Resources\Pages\Concerns;

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

    public function mountHasRelationManagers(): void
    {
        $managers = $this->getRelationManagers();

        if (array_key_exists($this->activeRelationManager, $managers)) {
            return;
        }

        $this->activeRelationManager = array_key_first($this->getRelationManagers()) ?? null;
    }
}
