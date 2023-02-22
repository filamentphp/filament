@props([
    'activeManager',
    'content' => null,
    'contentTabLabel' => null,
    'managers',
    'ownerRecord',
    'pageClass',
])

<div class="filament-resources-relation-managers-container space-y-2">
    @if ((count($managers) > 1) || $content)
        <div class="flex justify-center">
            <x-filament::tabs>
                @php
                    $tabs = $managers;

                    if ($content) {
                        $tabs = array_replace([null => null], $tabs);
                    }
                @endphp

                @foreach ($tabs as $tabKey => $manager)
                    @php
                        $activeManager = strval($activeManager);
                        $tabKey = strval($tabKey);
                        $isGroup = $manager instanceof \Filament\Resources\RelationManagers\RelationGroup;

                        if ($isGroup) {
                            $manager->ownerRecord($ownerRecord);
                            $manager->pageClass($pageClass);
                        }
                    @endphp

                    <x-filament::tabs.item
                        :wire:click="'$set(\'activeRelationManager\', ' . (filled($tabKey) ? ('\'' . $tabKey . '\'') : 'null') . ')'"
                        :active="$activeManager === $tabKey"
                        :badge="filled($tabKey) ? ($isGroup ? $manager->getBadge() : $manager::getBadge($ownerRecord, $pageClass)) : null"
                        :icon="filled($tabKey) ? ($isGroup ? $manager->getIcon() : $manager::getIcon($ownerRecord, $pageClass)) : null"
                    >
                        @if (filled($tabKey))
                            {{ $isGroup ? $manager->getLabel() : $manager::getTitle($ownerRecord, $pageClass) }}
                        @elseif ($content)
                            {{ $contentTabLabel }}
                        @endif
                    </x-filament::tabs.item>
                @endforeach
            </x-filament::tabs>
        </div>
    @endif

    @if (filled($activeManager) && isset($managers[$activeManager]))
        <div
            @if (count($managers) > 1)
                id="relationManager{{ ucfirst($activeManager) }}"
                role="tabpanel"
                tabindex="0"
            @endif
            class="space-y-4 outline-none"
        >
            @if ($managers[$activeManager] instanceof \Filament\Resources\RelationManagers\RelationGroup)
                @foreach($managers[$activeManager]->ownerRecord($ownerRecord)->pageClass($pageClass)->getManagers() as $groupedManager)
                    @livewire(\Livewire\Livewire::getAlias($groupedManager, $groupedManager::getName()), ['ownerRecord' => $ownerRecord, 'pageClass' => $pageClass], key($groupedManager))
                @endforeach
            @else
                @php
                    $manager = $managers[$activeManager];
                @endphp

                @livewire(\Livewire\Livewire::getAlias($manager, $manager::getName()), ['ownerRecord' => $ownerRecord, 'pageClass' => $pageClass], key($manager))
            @endif
        </div>
    @elseif ($content)
        {{ $content }}
    @endif
</div>
