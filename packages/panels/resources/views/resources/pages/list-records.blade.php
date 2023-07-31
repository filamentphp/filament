<x-filament-panels::page
    @class([
        'fi-resource-list-records-page',
        'fi-resource-' . str_replace('/', '-', $this->getResource()::getSlug()),
    ])
>
    <div class="flex flex-col gap-y-4">
        @if (count($tabs = $this->getTabs()))
            <div class="mx-auto flex max-w-full flex-col gap-4 md:flex-row">
                {{ \Filament\Support\Facades\FilamentView::renderHook('panels::resource.pages.list-records.tabs.before', scopes: $this->getRenderHookScopes()) }}

                <div
                    class="rounded-xl bg-white p-2 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10"
                >
                    <x-filament::tabs class="self-center">
                        {{ \Filament\Support\Facades\FilamentView::renderHook('panels::resource.pages.list-records.tabs.start', scopes: $this->getRenderHookScopes()) }}

                        @foreach ($tabs as $tabKey => $tab)
                            @php
                                $activeTab = strval($activeTab);
                                $tabKey = strval($tabKey);
                            @endphp

                            <x-filament::tabs.item
                                :active="$activeTab === $tabKey"
                                :badge="$tab->getBadge()"
                                :icon="$tab->getIcon()"
                                :icon-position="$tab->getIconPosition()"
                                :wire:click="'$set(\'activeTab\', ' . (filled($tabKey) ? ('\'' . $tabKey . '\'') : 'null') . ')'"
                            >
                                {{ $tab->getLabel() ?? $this->generateTabLabel($tabKey) }}
                            </x-filament::tabs.item>
                        @endforeach

                        {{ \Filament\Support\Facades\FilamentView::renderHook('panels::resource.pages.list-records.tabs.end', scopes: $this->getRenderHookScopes()) }}
                    </x-filament::tabs>
                </div>

                {{ \Filament\Support\Facades\FilamentView::renderHook('panels::resource.pages.list-records.tabs.after', scopes: $this->getRenderHookScopes()) }}
            </div>
        @endif

        {{ \Filament\Support\Facades\FilamentView::renderHook('panels::resource.pages.list-records.table.before', scopes: $this->getRenderHookScopes()) }}

        {{ $this->table }}

        {{ \Filament\Support\Facades\FilamentView::renderHook('panels::resource.pages.list-records.table.after', scopes: $this->getRenderHookScopes()) }}
    </div>
</x-filament-panels::page>
