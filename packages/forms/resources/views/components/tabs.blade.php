<div
    x-data="{

        tab: null,

        init: function () {
            this.tab = this.getTabs()[@js($getActiveTab()) - 1]
        },

        getTabs: function () {
            return JSON.parse(this.$refs.tabsData.value)
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
            ->class(['filament-forms-tabs-component rounded-xl shadow-sm border border-gray-300 bg-white dark:bg-gray-800 dark:border-gray-700'])
    }}
>
    <input
        type="hidden"
        value='{{
            collect($getChildComponentContainer()->getComponents())
                ->filter(static fn (\Filament\Forms\Components\Tabs\Tab $tab): bool => ! $tab->isHidden())
                ->map(static fn (\Filament\Forms\Components\Tabs\Tab $tab) => $tab->getId())
                ->values()
                ->toJson()
        }}'
        x-ref="tabsData"
    />

    <div
        @if ($label = $getLabel()) aria-label="{{ $label }}" @endif
        role="tablist"
        class="filament-forms-tabs-component-header rounded-t-xl flex overflow-y-auto bg-gray-100 dark:bg-gray-700"
    >
        @foreach ($getChildComponentContainer()->getComponents() as $tab)
            @php
                $tabId = $tab->getId();
            @endphp

            <button
                type="button"
                aria-controls="{{ $tabId }}"
                x-bind:aria-selected="tab === '{{ $tabId }}'"
                x-on:click="tab = '{{ $tabId }}'"
                role="tab"
                x-bind:tabindex="tab === '{{ $tabId }}' ? 0 : -1"
                class="filament-forms-tabs-component-button flex items-center gap-2 shrink-0 p-3 text-sm font-medium"
                x-bind:class="{
                    'text-gray-500 dark:text-gray-400': tab !== '{{ $tabId }}',
                    'filament-forms-tabs-component-button-active bg-white text-primary-600 dark:bg-gray-800': tab === '{{ $tabId }}',
                }"
            >
                @if ($icon = $tab->getIcon())
                    <x-filament::icon
                        :name="$icon"
                        alias="filament-forms::components.tabs.button"
                        size="h-5 w-5"
                    />
                @endif

                <span>{{ $tab->getLabel() }}</span>

                @if ($badge = $tab->getBadge())
                    <span
                        class="inline-flex items-center justify-center ml-auto rtl:ml-0 rtl:mr-auto min-h-4 px-2 py-0.5 text-xs font-medium tracking-tight rounded-xl whitespace-normal"
                        x-bind:class="{
                            'bg-gray-200 dark:bg-gray-600': tab !== '{{ $tabId }}',
                            'bg-primary-500/10 font-medium': tab === '{{ $tabId }}',
                        }"
                    >
                        {{ $badge }}
                    </span>
                @endif
            </button>
        @endforeach
    </div>

    @foreach ($getChildComponentContainer()->getComponents() as $tab)
        {{ $tab }}
    @endforeach
</div>
