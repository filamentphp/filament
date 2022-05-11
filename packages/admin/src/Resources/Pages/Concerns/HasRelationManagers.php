<?php

namespace Filament\Resources\Pages\Concerns;

trait HasRelationManagers
{
    public $activeRelationManager = null;

    protected function getRelationManagers(): array
    {
        $managers = $this->getResource()::getRelations();

        if (empty($managers) || !is_array($managers[0])) {
            return $this->filterForViewableRelationManagers($managers);
        }

        for($i = 0; $i < count($managers); $i++) {
            $managers[$i] = $this->filterForViewableRelationManagers($managers[$i]);
        }

        return $managers;

    }

    public function mountHasRelationManagers(): void
    {
        $managers = $this->getRelationManagers();

        if (array_key_exists($this->activeRelationManager, $managers)) {
            return;
        }

        $this->activeRelationManager = array_key_first($this->getRelationManagers()) ?? null;
    }

    private function filterForViewableRelationManagers(array $managers): array
    {
        return array_filter(
            $managers,
            fn (string $manager): bool => $manager::canViewForRecord($this->record),
        );
    }
}
