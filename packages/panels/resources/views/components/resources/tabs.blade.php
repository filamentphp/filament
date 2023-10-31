@if (count($tabs = $this->getCachedTabs()))
    @php
        $activeTab = strval($this->activeTab);
        $renderHookScopes = $this->getRenderHookScopes();
    @endphp

    <x-filament::tabs>
        {{ \Filament\Support\Facades\FilamentView::renderHook('panels::resource.tabs.start', scopes: $renderHookScopes) }}
        {{ \Filament\Support\Facades\FilamentView::renderHook('panels::resource.pages.list-records.tabs.start', scopes: $renderHookScopes) }}

        @foreach ($tabs as $tabKey => $tab)
            @php
                $tabKey = strval($tabKey);
            @endphp

            <x-filament::tabs.item
                :active="$activeTab === $tabKey"
                :badge="$tab->getBadge()"
                :badge-color="$tab->getBadgeColor()"
                :icon="$tab->getIcon()"
                :icon-position="$tab->getIconPosition()"
                :wire:click="'$set(\'activeTab\', ' . (filled($tabKey) ? ('\'' . $tabKey . '\'') : 'null') . ')'"
            >
                {{ $tab->getLabel() ?? $this->generateTabLabel($tabKey) }}
            </x-filament::tabs.item>
        @endforeach

        {{ \Filament\Support\Facades\FilamentView::renderHook('panels::resource.tabs.end', scopes: $renderHookScopes) }}
        {{ \Filament\Support\Facades\FilamentView::renderHook('panels::resource.pages.list-records.tabs.end', scopes: $renderHookScopes) }}
    </x-filament::tabs>
@endif
