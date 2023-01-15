<?php

namespace Filament\Resources\Pages\Concerns;

use Filament\Resources\RelationManagers\RelationGroup;

trait HasRelationManagers
{
    public ?string $activeRelationManager = null;

    /**
     * @return array<string | RelationGroup>
     */
    public function getRelationManagers(): array
    {
        $managers = $this->getResource()::getRelations();

        return array_filter(
            $managers,
            function (string | RelationGroup $manager): bool {
                if ($manager instanceof RelationGroup) {
                    return (bool) count($manager->ownerRecord($this->getRecord())->pageClass(static::class)->getManagers());
                }

                return $manager::canViewForRecord($this->getRecord(), static::class);
            },
        );
    }

    public function mountHasRelationManagers(): void
    {
        $managers = $this->getRelationManagers();

        if (array_key_exists($this->activeRelationManager, $managers) || $this->hasCombinedRelationManagerTabsWithContent()) {
            return;
        }

        $this->activeRelationManager = array_key_first($this->getRelationManagers()) ?? null;
    }

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return false;
    }

    public function getContentTabLabel(): ?string
    {
        return null;
    }
}
