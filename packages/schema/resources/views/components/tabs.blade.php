@php
    use Filament\Schema\Components\Tabs\Tab;

    $isContained = $isContained();
    $isVertical = $isVertical();
@endphp

<div
    wire:ignore.self
    x-cloak
    x-data="{
        tab: @if ($isTabPersisted() && filled($persistenceKey = $getKey())) $persist(null).as('tabs-{{ $persistenceKey }}') @else null @endif,

        getTabs: function () {
            if (! this.$refs.tabsData) {
                return []
            }

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
    x-init="
        $watch('tab', () => updateQueryString())

        const tabs = getTabs()

        if (! tab || ! tabs.includes(tab)) {
            tab = tabs[@js($getActiveTab()) - 1]
        }

        Livewire.hook('commit', ({ component, commit, succeed, fail, respond }) => {
            succeed(({ snapshot, effect }) => {
                $nextTick(() => {
                    if (component.id !== @js($this->getId())) {
                        return
                    }

                    const tabs = getTabs()

                    if (! tabs.includes(tab)) {
                        tab = tabs[@js($getActiveTab()) - 1] ?? tab
                    }
                })
            })
        })
    "
    {{
        $attributes
            ->merge([
                'id' => $getId(),
                'wire:key' => $getLivewireKey() . '.container',
            ], escape: false)
            ->merge($getExtraAttributes(), escape: false)
            ->merge($getExtraAlpineAttributes(), escape: false)
            ->class([
                'fi-fo-tabs',
                'fi-contained rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10' => $isContained,
                'flex' => $isVertical,
            ])
    }}
>
    <input
        type="hidden"
        value="{{
            collect($getChildComponentContainer()->getComponents())
                ->filter(static fn (Tab $tab): bool => $tab->isVisible())
                ->map(static fn (Tab $tab) => $tab->getKey())
                ->values()
                ->toJson()
        }}"
        x-ref="tabsData"
    />

    <x-filament::tabs :contained="$isContained" :vertical="$isVertical" :label="$getLabel()">
        @foreach ($getChildComponentContainer()->getComponents() as $tab)
            @php
                $tabKey = $tab->getKey();
            @endphp

            <x-filament::tabs.item
                :alpine-active="'tab === \'' . $tabKey . '\''"
                :badge="$tab->getBadge()"
                :badge-color="$tab->getBadgeColor()"
                :badge-icon="$tab->getBadgeIcon()"
                :badge-icon-position="$tab->getBadgeIconPosition()"
                :icon="$tab->getIcon()"
                :icon-position="$tab->getIconPosition()"
                :x-on:click="'tab = \'' . $tabKey . '\''"
            >
                {{ $tab->getLabel() }}
            </x-filament::tabs.item>
        @endforeach
    </x-filament::tabs>

    @foreach ($getChildComponentContainer()->getComponents() as $tab)
        {{ $tab }}
    @endforeach
</div>
