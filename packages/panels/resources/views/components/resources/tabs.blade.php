@if (count($tabs = $this->getCachedTabs()))
    @php
        $activeTab = strval($this->activeTab);
        $renderHookScopes = $this->getRenderHookScopes();
    @endphp

    <x-filament::tabs>
        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::RESOURCE_TABS_START, scopes: $renderHookScopes) }}
        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::RESOURCE_PAGES_LIST_RECORDS_TABS_START, scopes: $renderHookScopes) }}

        @foreach ($tabs as $tabKey => $tab)
            @php
                $tabKey = strval($tabKey);
            @endphp

            <x-filament::tabs.item
                :active="$activeTab === $tabKey"
                :badge="$tab->getBadge()"
                :badge-color="$tab->getBadgeColor()"
                :badge-icon="$tab->getBadgeIcon()"
                :badge-icon-position="$tab->getBadgeIconPosition()"
                :icon="$tab->getIcon()"
                :icon-position="$tab->getIconPosition()"
                :wire:click="'$set(\'activeTab\', ' . (filled($tabKey) ? ('\'' . $tabKey . '\'') : 'null') . ')'"
                :attributes="$tab->getExtraAttributeBag()"
            >
                {{ $tab->getLabel() ?? $this->generateTabLabel($tabKey) }}
            </x-filament::tabs.item>
        @endforeach

        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::RESOURCE_TABS_END, scopes: $renderHookScopes) }}
        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::RESOURCE_PAGES_LIST_RECORDS_TABS_END, scopes: $renderHookScopes) }}
    </x-filament::tabs>
@endif
