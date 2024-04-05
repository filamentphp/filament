<?php

namespace Filament\Resources\Pages\Concerns;

use Filament\Resources\Pages\ContentTabPosition;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\RelationManagers\RelationManagerConfiguration;
use Livewire\Attributes\Url;

trait HasRelationManagers
{
    #[Url]
    public ?string $activeRelationManager = null;

    /**
     * @return array<class-string<RelationManager> | RelationGroup | RelationManagerConfiguration>
     */
    public function getRelationManagers(): array
    {
        $managers = $this->getResource()::getRelations();

        return array_filter(
            $managers,
            function (string | RelationGroup | RelationManagerConfiguration $manager): bool {
                if ($manager instanceof RelationGroup) {
                    return (bool) count($manager->ownerRecord($this->getRecord())->pageClass(static::class)->getManagers());
                }

                return $this->normalizeRelationManagerClass($manager)::canViewForRecord($this->getRecord(), static::class);
            },
        );
    }

    /**
     * @param  class-string<RelationManager> | RelationManagerConfiguration  $manager
     * @return class-string<RelationManager>
     */
    protected function normalizeRelationManagerClass(string | RelationManagerConfiguration $manager): string
    {
        if ($manager instanceof RelationManagerConfiguration) {
            return $manager->relationManager;
        }

        return $manager;
    }

    public function renderingHasRelationManagers(): void
    {
        $managers = $this->getRelationManagers();

        if (array_key_exists($this->activeRelationManager, $managers)) {
            return;
        }

        if ($this->hasCombinedRelationManagerTabsWithContent()) {
            return;
        }

        $this->activeRelationManager = array_key_first($managers);
    }

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return false;
    }

    public function getContentTabLabel(): ?string
    {
        return null;
    }

    public function getContentTabIcon(): ?string
    {
        return null;
    }

    public function getContentTabPosition(): ?ContentTabPosition
    {
        return null;
    }
}
