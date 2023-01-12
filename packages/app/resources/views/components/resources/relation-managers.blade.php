@props([
    'activeManager',
    'form' => null,
    'formTabLabel' => null,
    'managers',
    'ownerRecord',
    'pageClass',
])

<div class="filament-resources-relation-managers-container space-y-2">
    @if ((count($managers) > 1) || $form)
        <div class="flex justify-center">
            <x-filament::tabs>
                @php
                    $tabs = $managers;

                    if ($form) {
                        $tabs = array_replace([null => null], $tabs);
                    }
                @endphp

                @foreach ($tabs as $tabKey => $manager)
                    @php
                        $activeManager = strval($activeManager);
                        $tabKey = strval($tabKey);
                    @endphp

                    <x-filament::tabs.item
                        :wire:click="'$set(\'activeRelationManager\', ' . (filled($tabKey) ? ('\'' . $tabKey . '\'') : 'null') . ')'"
                        :active="$activeManager === $tabKey"
                    >
                        @if (filled($tabKey))
                            {{ $manager instanceof \Filament\Resources\RelationManagers\RelationGroup ? $manager->getLabel() : $manager::getTitle($ownerRecord, static::class) }}
                        @elseif ($form)
                            {{ $formTabLabel }}
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
            class="space-y-4 focus:outline-none"
        >
            @if ($managers[$activeManager] instanceof \Filament\Resources\RelationManagers\RelationGroup)
                @foreach($managers[$activeManager]->getManagers(ownerRecord: $ownerRecord) as $groupedManager)
                    @livewire(\Livewire\Livewire::getAlias($groupedManager, $groupedManager::getName()), ['ownerRecord' => $ownerRecord], key($groupedManager))
                @endforeach
            @else
                @php
                    $manager = $managers[$activeManager];
                @endphp

                @livewire(\Livewire\Livewire::getAlias($manager, $manager::getName()), ['ownerRecord' => $ownerRecord, 'pageClass' => $pageClass], key($manager))
            @endif
        </div>
    @elseif ($form)
        {{ $form }}
    @endif
</div>
