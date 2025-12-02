<?php

namespace Filament\Resources\Pages\Concerns;

use BackedEnum;
use Filament\Resources\Pages\Enums\ContentTabPosition;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\RelationManagers\RelationManagerConfiguration;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Arr;
use Livewire\Attributes\Url;

trait HasRelationManagers
{
    #[Url(as: 'relation')]
    public ?string $activeRelationManager = null;

    /**
     * @return array<class-string<RelationManager> | RelationGroup | RelationManagerConfiguration>
     */
    protected function getAllRelationManagers(): array
    {
        return $this->getResource()::getRelations();
    }

    /**
     * @return array<class-string<RelationManager> | RelationGroup | RelationManagerConfiguration>
     */
    public function getRelationManagers(): array
    {
        if (! $this->hasRecord()) {
            return [];
        }

        $managers = $this->getAllRelationManagers();

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

    public function getContentTabComponent(): Tab
    {
        return Tab::make($this->getContentTabLabel())
            ->icon($this->getContentTabIcon());
    }

    public function getContentTabLabel(): ?string
    {
        return null;
    }

    public function getContentTabIcon(): string | BackedEnum | Htmlable | null
    {
        return null;
    }

    public function getContentTabPosition(): ?ContentTabPosition
    {
        return null;
    }

    public function getRelationManagersContentComponent(): Component
    {
        $managers = $this->getRelationManagers();
        $hasCombinedRelationManagerTabsWithContent = $this->hasCombinedRelationManagerTabsWithContent();
        $ownerRecord = $this->getRecord();

        $managerLivewireData = ['ownerRecord' => $ownerRecord, 'pageClass' => static::class];

        if ($activeLocale = (property_exists($this, 'activeLocale') ? $this->activeLocale : null)) {
            $managerLivewireData['activeLocale'] = $activeLocale;
        }

        if ((count($managers) > 1) || $hasCombinedRelationManagerTabsWithContent) {
            $tabs = $managers;

            if ($hasCombinedRelationManagerTabsWithContent) {
                match ($this->getContentTabPosition()) {
                    ContentTabPosition::After => $tabs = array_merge($tabs, [null => null]),
                    default => $tabs = array_replace([null => null], $tabs),
                };
            }

            $tabs = collect($tabs)
                ->map(function ($manager, string | int $tabKey) use ($hasCombinedRelationManagerTabsWithContent, $managerLivewireData, $ownerRecord): Tab {
                    $tabKey = strval($tabKey);

                    if (blank($tabKey) && $hasCombinedRelationManagerTabsWithContent) {
                        return $this->getContentTabComponent();
                    }

                    if ($manager instanceof RelationGroup) {
                        $manager->ownerRecord($ownerRecord);
                        $manager->pageClass(static::class);

                        return $manager->getTabComponent()
                            ->schema(fn (): array => collect($manager->getManagers())
                                ->map(fn ($groupedManager, $groupedManagerKey): Livewire => Livewire::make(
                                    $normalizedGroupedManagerClass = $this->normalizeRelationManagerClass($groupedManager),
                                    [...$managerLivewireData, ...(($groupedManager instanceof RelationManagerConfiguration) ? [...$groupedManager->relationManager::getDefaultProperties(), ...$groupedManager->getProperties()] : $groupedManager::getDefaultProperties())],
                                )->key("{$normalizedGroupedManagerClass}-{$groupedManagerKey}"))
                                ->all());
                    }

                    $normalizedManagerClass = $this->normalizeRelationManagerClass($manager);

                    return $normalizedManagerClass::getTabComponent($ownerRecord, static::class)
                        ->schema(fn (): array => [
                            Livewire::make(
                                $normalizedManagerClass,
                                [...$managerLivewireData, ...(($manager instanceof RelationManagerConfiguration) ? [...$manager->relationManager::getDefaultProperties(), ...$manager->getProperties()] : $manager::getDefaultProperties())],
                            )->key($normalizedManagerClass),
                        ]);
                })
                ->all();

            return Tabs::make()
                ->livewireProperty('activeRelationManager')
                ->contained(false)
                ->tabs($tabs);
        }

        if (empty($managers)) {
            return Group::make()->hidden();
        }

        $manager = Arr::first($managers);

        if ($manager instanceof RelationGroup) {
            $manager->ownerRecord($ownerRecord);
            $manager->pageClass(static::class);

            return Group::make(collect($manager->ownerRecord($ownerRecord)->pageClass(static::class)->getManagers())
                ->map(fn ($groupedManager, $groupedManagerKey): Livewire => Livewire::make(
                    $normalizedGroupedManagerClass = $this->normalizeRelationManagerClass($groupedManager),
                    [...$managerLivewireData, ...(($groupedManager instanceof RelationManagerConfiguration) ? [...$groupedManager->relationManager::getDefaultProperties(), ...$groupedManager->getProperties()] : $groupedManager::getDefaultProperties())],
                )->key("{$normalizedGroupedManagerClass}-{$groupedManagerKey}"))
                ->all());
        }

        return Livewire::make(
            $normalizedManagerClass = $this->normalizeRelationManagerClass($manager),
            [...$managerLivewireData, ...(($manager instanceof RelationManagerConfiguration) ? [...$manager->relationManager::getDefaultProperties(), ...$manager->getProperties()] : $manager::getDefaultProperties())],
        )->key($normalizedManagerClass);
    }
}
