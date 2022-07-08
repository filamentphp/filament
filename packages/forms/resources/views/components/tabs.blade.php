<div
    x-data="{

        tab: null,

        init: function () {
            this.tab = this.getTabs()[0]
        },

        getTabs: function () {
            return JSON.parse(this.$refs.tabsData.value)
        },

    }"
    x-on:expand-concealing-component.window="
        if (getTabs().includes($event.detail.id)) {
            tab = $event.detail.id
            $el.scrollIntoView({ behavior: 'smooth', block: 'start' })
        }
    "
    x-cloak
    {!! $getId() ? "id=\"{$getId()}\"" : null !!}
    {{ $attributes->merge($getExtraAttributes())->class([
        'rounded-xl shadow-sm border border-gray-300 bg-white filament-forms-tabs-component',
        'dark:bg-gray-800 dark:border-gray-700' => config('forms.dark_mode'),
    ]) }}
    {{ $getExtraAlpineAttributeBag() }}
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
        {!! $getLabel() ? 'aria-label="' . $getLabel() . '"' : null !!}
        role="tablist"
        @class([
            'rounded-t-xl flex overflow-y-auto bg-gray-100',
            'dark:bg-gray-700' => config('forms.dark_mode'),
        ])
    >
        @foreach ($getChildComponentContainer()->getComponents() as $tab)
            <button
                type="button"
                aria-controls="{{ $tab->getId() }}"
                x-bind:aria-selected="tab === '{{ $tab->getId() }}'"
                x-on:click="tab = '{{ $tab->getId() }}'"
                role="tab"
                x-bind:tabindex="tab === '{{ $tab->getId() }}' ? 0 : -1"
                class="shrink-0 p-3 text-sm font-medium"
                x-bind:class="{ 'bg-white @if (config('forms.dark_mode')) dark:bg-gray-800 @endif': tab === '{{ $tab->getId() }}' }"
            >
                @if ($tab->hasIcon())
                    <x-dynamic-component :component="$tab->getIcon()" class="h-5 w-5 mr-1 text-gray-500 @if (config('forms.dark_mode')) dark:text-gray-400 @endif inline"/>
                @endif

                {{ $tab->getLabel() }}
            </button>
        @endforeach
    </div>

    @foreach ($getChildComponentContainer()->getComponents() as $tab)
        {{ $tab }}
    @endforeach
</div>
