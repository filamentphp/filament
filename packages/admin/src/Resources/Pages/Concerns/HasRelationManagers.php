<?php

namespace Filament\Resources\Pages\Concerns;

trait HasRelationManagers
{
    public $activeRelationManager = null;

    protected function getRelationManagers(): array
    {
        $managers = $this->getResource()::getRelations();

        return array_values(array_filter(
            $managers,
            fn (string $manager): bool => $manager::canViewForRecord($this->record),
        ));
    }

    public function mountHasRelationManagers(): void
    {
        $managers = $this->getRelationManagers();

        if (in_array($this->activeRelationManager, $managers)) {
            return;
        }

        $this->activeRelationManager = $this->getRelationManagers()[0] ?? null;
    }
}
