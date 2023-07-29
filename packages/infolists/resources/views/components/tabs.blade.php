<div
    x-cloak
    x-data="{
        tab: null,

        init: function () {
            this.$watch('tab', () => this.updateQueryString())

            this.tab = @js(collect($getChildComponentContainer()->getComponents())
                        ->filter(static fn (\Filament\Infolists\Components\Tabs\Tab $tab): bool => $tab->isVisible())
                        ->map(static fn (\Filament\Infolists\Components\Tabs\Tab $tab) => $tab->getId())
                        ->values()
                        ->get($getActiveTab() - 1))
        },

        updateQueryString: function () {
            if (! @js($isTabPersistedInQueryString())) {
                return
            }

            const url = new URL(window.location.href)
            url.searchParams.set(@js($getTabQueryStringKey()), this.tab)

            history.pushState(null, document.title, url.toString())
        },
    }"
    {{
        $attributes
            ->merge([
                'id' => $getId(),
            ], escape: false)
            ->merge($getExtraAttributes(), escape: false)
            ->merge($getExtraAlpineAttributes(), escape: false)
            ->class(['fi-in-tabs rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10'])
    }}
>
    <div class="border-b border-gray-200 px-3 py-2.5 dark:border-white/10">
        <x-filament::tabs :label="$getLabel()">
            @foreach ($getChildComponentContainer()->getComponents() as $tab)
                @php
                    $tabId = $tab->getId();
                @endphp

                <x-filament::tabs.item
                    :alpine-active="'tab === \'' . $tabId . '\''"
                    :badge="$tab->getBadge()"
                    :icon="$tab->getIcon()"
                    :icon-position="$tab->getIconPosition()"
                    :x-on:click="'tab = \'' . $tabId . '\''"
                >
                    {{ $tab->getLabel() }}
                </x-filament::tabs.item>
            @endforeach
        </x-filament::tabs>
    </div>

    @foreach ($getChildComponentContainer()->getComponents() as $tab)
        {{ $tab }}
    @endforeach
</div>
