<div
    x-data="{ tab: '{{ count($getTabsConfig()) ? array_key_first($getTabsConfig()) : null }}', tabs: {{ json_encode($getTabsConfig()) }} }"
    x-on:expand-concealing-component.window="if ($event.detail.id in tabs) tab = $event.detail.id"
    x-cloak
    {!! $getId() ? "id=\"{$getId()}\"" : null !!}
    {{ $attributes->merge($getExtraAttributes())->class([
        'rounded-xl shadow-sm border border-gray-300 bg-white filament-forms-tabs-component',
        'dark:bg-gray-700 dark:border-gray-600' => config('forms.dark_mode'),
    ]) }}
    {{ $getExtraAlpineAttributeBag() }}
>
    <div
        {!! $getLabel() ? 'aria-label="' . $getLabel() . '"' : null !!}
        role="tablist"
        @class([
            'rounded-t-xl flex overflow-y-auto bg-gray-100',
            'dark:bg-gray-800' => config('forms.dark_mode'),
        ])
    >
        @foreach ($getTabsConfig() as $tabId => $tabLabel)
            <button
                type="button"
                aria-controls="{{ $tabId }}"
                x-bind:aria-selected="tab === '{{ $tabId }}'"
                x-on:click="tab = '{{ $tabId }}'"
                role="tab"
                x-bind:tabindex="tab === '{{ $tabId }}' ? 0 : -1"
                class="shrink-0 p-3 text-sm font-medium"
                x-bind:class="{ 'bg-white @if (config('forms.dark_mode')) dark:bg-gray-700 @endif': tab === '{{ $tabId }}' }"
            >
                {{ $tabLabel }}
            </button>
        @endforeach
    </div>

    @foreach ($getChildComponentContainer()->getComponents() as $tab)
        {{ $tab }}
    @endforeach
</div>
