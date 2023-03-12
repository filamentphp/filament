<div
    x-data="{

        tab: null,

        init: function () {
            this.$watch('tab', () => this.updateQueryString())

            this.tab = this.getTabs()[@js($getActiveTab()) - 1]
        },

        getTabs: function () {
            return JSON.parse(this.$refs.tabsData.value)
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
                'wire:key' => "{$this->id}.{$getStatePath()}." . \Filament\Forms\Components\Tabs::class . '.container',
            ], escape: false)
            ->merge($getExtraAttributes(), escape: false)
            ->merge($getExtraAlpineAttributes(), escape: false)
            ->class(['filament-forms-tabs-component rounded-xl shadow-sm bg-white ring-1 ring-gray-900/10 dark:bg-gray-800 dark:ring-gray-50/10'])
    }}
>
    <input
        type="hidden"
        value='{{
            collect($getChildComponentContainer()->getComponents())
                ->filter(static fn (\Filament\Forms\Components\Tabs\Tab $tab): bool => $tab->isVisible())
                ->map(static fn (\Filament\Forms\Components\Tabs\Tab $tab) => $tab->getId())
                ->values()
                ->toJson()
        }}'
        x-ref="tabsData"
    />

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
