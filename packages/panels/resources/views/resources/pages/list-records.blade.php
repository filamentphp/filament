<x-filament::page
    @class([
        'fi-resource-list-records-page',
        'fi-resource-' . str_replace('/', '-', $this->getResource()::getSlug()),
    ])
>
    <div class="flex flex-col gap-y-4">
        @if (count($tabs = $this->getTabs()))
            <x-filament::tabs class="self-center">
                @foreach ($tabs as $tabKey => $tab)
                    @php
                        $activeTab = strval($activeTab);
                        $tabKey = strval($tabKey);
                    @endphp

                    <x-filament::tabs.item
                        :active="$activeTab === $tabKey"
                        :badge="$tab->getBadge()"
                        :icon="$tab->getIcon()"
                        :icon-color="$tab->getIconColor()"
                        :icon-position="$tab->getIconPosition()"
                        :wire:click="'$set(\'activeTab\', ' . (filled($tabKey) ? ('\'' . $tabKey . '\'') : 'null') . ')'"
                    >
                        {{ $tab->getLabel() ?? $this->generateTabLabel($tabKey) }}
                    </x-filament::tabs.item>
                @endforeach
            </x-filament::tabs>
        @endif

        {{ \Filament\Support\Facades\FilamentView::renderHook('resource.pages.list-records.table.start', scope: static::class) }}

        {{ $this->table }}

        {{ \Filament\Support\Facades\FilamentView::renderHook('resource.pages.list-records.table.end', scope: static::class) }}
    </div>
</x-filament::page>
