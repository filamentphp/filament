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
     * @var array<class-string<RelationManager> | RelationGroup | RelationManagerConfiguration> | null
     */
    protected ?array $cachedRelationManagers = null;

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
    public function getCachedRelationManagers(): array
    {
        if (! $this->hasRecord()) {
            return [];
        }

        return $this->cachedRelationManagers ??= $this->getRelationManagers();
    }

    /**
     * @return array<class-string<RelationManager> | RelationGroup | RelationManagerConfiguration>
     */
    public function getRelationManagers(): array
    {
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
        $managers = $this->getCachedRelationManagers();

        if (array_key_exists($this->activeRelationManager ?? '', $managers)) {
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

    /**
     * @return array<string | int, Tab | null>
     */
    public function getTabComponents(): array
    {
        // The empty key has always represented content in combined tabs.
        $tabs = $this->getRelationManagerTabComponents(except: ['']);
        $contentTab = ['' => $this->getContentTabComponent()];

        return match ($this->getContentTabPosition()) {
            ContentTabPosition::After => $tabs + $contentTab,
            default => $contentTab + $tabs,
        };
    }

    /**
     * @param  array<string | int>  $except
     * @return array<string | int, Tab>
     */
    public function getRelationManagerTabComponents(array $except = []): array
    {
        $tabs = [];

        foreach (array_keys(array_diff_key($this->getCachedRelationManagers(), array_flip($except))) as $managerKey) {
            if ($tab = $this->getRelationManagerTabComponent($managerKey)) {
                $tabs[$managerKey] = $tab;
            }
        }

        return $tabs;
    }

    public function getRelationManagerTabComponent(string | int $key): ?Tab
    {
        $manager = $this->getCachedRelationManagers()[$key] ?? null;

        if (! $manager) {
            return null;
        }

        $ownerRecord = $this->getRecord();
        $managerLivewireData = $this->getRelationManagerLivewireData();

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
    }

    /**
     * @return array<string, mixed>
     */
    protected function getRelationManagerLivewireData(): array
    {
        $managerLivewireData = ['ownerRecord' => $this->getRecord(), 'pageClass' => static::class];

        if ($activeLocale = (property_exists($this, 'activeLocale') ? $this->activeLocale : null)) {
            $managerLivewireData['activeLocale'] = $activeLocale;
        }

        return $managerLivewireData;
    }

    public function getRelationManagersContentComponent(): Component
    {
        $managers = $this->getCachedRelationManagers();
        $hasCombinedRelationManagerTabsWithContent = $this->hasCombinedRelationManagerTabsWithContent();

        if ((count($managers) > 1) || $hasCombinedRelationManagerTabsWithContent) {
            return Tabs::make()
                ->key('relationManagerTabs')
                ->livewireProperty('activeRelationManager')
                ->contained(false)
                ->tabs($hasCombinedRelationManagerTabsWithContent
                    ? fn (): array => array_filter($this->getTabComponents())
                    : $this->getRelationManagerTabComponents());
        }

        if (empty($managers)) {
            return Group::make()->hidden();
        }

        $ownerRecord = $this->getRecord();
        $managerLivewireData = $this->getRelationManagerLivewireData();
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
