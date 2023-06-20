<div
    x-data="{

        tab: null,

        init: function () {
            this.$watch('tab', () => this.updateQueryString())

            this.tab = @js(collect($getChildComponentContainer()->getComponents())
                ->filter(static fn (\Filament\Infolists\Components\Tabs\Tab $tab): bool => $tab->isVisible())
                ->get($getActiveTab() - 1)
                ->getId())
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
    x-cloak
    {{
        $attributes
            ->merge([
                'id' => $getId(),
            ], escape: false)
            ->merge($getExtraAttributes(), escape: false)
            ->merge($getExtraAlpineAttributes(), escape: false)
            ->class(['filament-infolists-tabs-component rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-800 dark:ring-white/20'])
    }}
>
    <div class="p-1">
        <x-filament::tabs :label="$getLabel()">
            @foreach ($getChildComponentContainer()->getComponents() as $tab)
                @php
                    $tabId = $tab->getId();
                @endphp

                <x-filament::tabs.item
                    :x-on:click="'tab = \'' . $tabId . '\''"
                    :alpine-active="'tab === \'' . $tabId . '\''"
                    :badge="$tab->getBadge()"
                    :icon="$tab->getIcon()"
                    :icon-color="$tab->getIconColor()"
                    :icon-position="$tab->getIconPosition()"
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
