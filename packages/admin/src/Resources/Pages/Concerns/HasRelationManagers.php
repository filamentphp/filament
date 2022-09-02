<?php

namespace Filament\Resources\Pages\Concerns;

use Filament\Resources\RelationManagers\RelationGroup;

trait HasRelationManagers
{
    public $activeRelationManager = null;

    protected function getRelationManagers(): array
    {
        $managers = $this->getResource()::getRelations();

        return array_filter(
            $managers,
            function (string | RelationGroup $manager): bool {
                if ($manager instanceof RelationGroup) {
                    return (bool) count($manager->getManagers(ownerRecord: $this->getRecord()));
                }

                return $manager::canViewForRecord($this->getRecord());
            },
        );
    }

    public function mountHasRelationManagers(): void
    {
        $managers = $this->getRelationManagers();

        if (array_key_exists($this->activeRelationManager, $managers) || $this->hasCombinedRelationManagerTabsWithForm()) {
            return;
        }

        $this->activeRelationManager = array_key_first($this->getRelationManagers()) ?? null;
    }

    public function hasCombinedRelationManagerTabsWithForm(): bool
    {
        return false;
    }

    public function getFormTabLabel(): ?string
    {
        return null;
    }
}
