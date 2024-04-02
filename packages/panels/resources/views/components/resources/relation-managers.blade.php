@props([
    'activeLocale' => null,
    'activeManager',
    'content' => null,
    'contentTabLabel' => null,
    'contentTabIcon' => null,
    'contentTabPosition' => null,
    'managers',
    'ownerRecord',
    'pageClass',
])

<div class="fi-resource-relation-managers flex flex-col gap-y-6">
    @php
        $activeManager = strval($activeManager);
        $normalizeRelationManagerClass = function (string | Filament\Resources\RelationManagers\RelationManagerConfiguration $manager): string {
            if ($manager instanceof \Filament\Resources\RelationManagers\RelationManagerConfiguration) {
                return $manager->relationManager;
            }

            return $manager;
        };
    @endphp

    @if ((count($managers) > 1) || $content)
        <x-filament::tabs>
            @php
                $tabs = $managers;

                if ($content) {
                    match ($contentTabPosition) {
                        \Filament\Resources\Pages\ContentTabPosition::After => $tabs = array_merge($tabs, [null => null]),
                        default => $tabs = array_replace([null => null], $tabs),
                    };
                }
            @endphp

            @foreach ($tabs as $tabKey => $manager)
                @php
                    $tabKey = strval($tabKey);
                    $isGroup = $manager instanceof \Filament\Resources\RelationManagers\RelationGroup;

                    if ($isGroup) {
                        $manager->ownerRecord($ownerRecord);
                        $manager->pageClass($pageClass);
                    } elseif (filled($tabKey)) {
                        $manager = $normalizeRelationManagerClass($manager);
                    }
                @endphp

                <x-filament::tabs.item
                    :active="$activeManager === $tabKey"
                    :badge="filled($tabKey) ? ($isGroup ? $manager->getBadge() : $manager::getBadge($ownerRecord, $pageClass)) : null"
                    :badge-color="filled($tabKey) ? ($isGroup ? $manager->getBadgeColor() : $manager::getBadgeColor($ownerRecord, $pageClass)) : null"
                    :badge-tooltip="filled($tabKey) ? ($isGroup ? $manager->getBadgeTooltip() : $manager::getBadgeTooltip($ownerRecord, $pageClass)) : null"
                    :icon="filled($tabKey) ? ($isGroup ? $manager->getIcon() : $manager::getIcon($ownerRecord, $pageClass)) : ($contentTabIcon ?? null)"
                    :icon-position="filled($tabKey) ? ($isGroup ? $manager->getIconPosition() : $manager::getIconPosition($ownerRecord, $pageClass)) : ($contentTabIconPosition ?? null)"
                    :wire:click="'$set(\'activeRelationManager\', ' . (filled($tabKey) ? ('\'' . $tabKey . '\'') : 'null') . ')'"
                >
                    @if (filled($tabKey))
                        {{ $isGroup ? $manager->getLabel() : $manager::getTitle($ownerRecord, $pageClass) }}
                    @elseif ($content)
                        {{ $contentTabLabel }}
                    @endif
                </x-filament::tabs.item>
            @endforeach
        </x-filament::tabs>
    @endif

    @if (filled($activeManager) && isset($managers[$activeManager]))
        <div
            @if (count($managers) > 1)
                id="relationManager{{ ucfirst($activeManager) }}"
                role="tabpanel"
                tabindex="0"
            @endif
            wire:key="{{ $this->getId() }}.relation-managers.active"
            class="flex flex-col gap-y-4"
        >
            @php
                $managerLivewireProperties = ['ownerRecord' => $ownerRecord, 'pageClass' => $pageClass];

                if (filled($activeLocale)) {
                    $managerLivewireProperties['activeLocale'] = $activeLocale;
                }
            @endphp

            @if ($managers[$activeManager] instanceof \Filament\Resources\RelationManagers\RelationGroup)
                @foreach ($managers[$activeManager]->ownerRecord($ownerRecord)->pageClass($pageClass)->getManagers() as $groupedManagerKey => $groupedManager)
                    @php
                        $normalizedGroupedManagerClass = $normalizeRelationManagerClass($groupedManager);
                    @endphp

                    @livewire(
                        $normalizedGroupedManagerClass,
                        [...$managerLivewireProperties, ...(($groupedManager instanceof \Filament\Resources\RelationManagers\RelationManagerConfiguration) ? [...$groupedManager->relationManager::getDefaultProperties(), ...$groupedManager->getProperties()] : $groupedManager::getDefaultProperties())],
                        key("{$normalizedGroupedManagerClass}-{$groupedManagerKey}"),
                    )
                @endforeach
            @else
                @php
                    $manager = $managers[$activeManager];
                    $normalizedManagerClass = $normalizeRelationManagerClass($manager);
                @endphp

                @livewire(
                    $normalizedManagerClass,
                    [...$managerLivewireProperties, ...(($manager instanceof \Filament\Resources\RelationManagers\RelationManagerConfiguration) ? [...$manager->relationManager::getDefaultProperties(), ...$manager->getProperties()] : $manager::getDefaultProperties())],
                    key($normalizedManagerClass),
                )
            @endif
        </div>
    @elseif ($content)
        {{ $content }}
    @endif
</div>
