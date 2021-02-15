@props([
    'label' => null,
    'id' => null,
    'tabs' => [],
])

<x-filament::card
    x-data="{ tab: '{{ count($tabs) ? array_key_first($tabs) : null }}', tabs: {{ json_encode($tabs) }} }"
    x-on:switch-tab.window="if ($event.detail in tabs) tab = $event.detail"
    x-cloak
    class="overflow-hidden"
>
    <div class="-m-4 md:-m-6">
        <div {{ $label ? "aria-label=\"{$label}\"" : null }} role="tablist" class="bg-gray-100 border-b border-gray-200 flex">
            @foreach ($tabs as $tabId => $tabLabel)
                <button type="button"
                    aria-controls="{{ $tabId }}-tab"
                    x-bind:aria-selected="tab === '{{ $tabId }}'"
                    x-on:click="tab = '{{ $tabId }}'"
                    role="tab"
                    x-bind:tabindex="tab === '{{ $tabId }}' ? 0 : -1"
                    class="text-sm leading-tight font-medium p-3 md:px-6 -mb-px border-r border-gray-200"
                    x-bind:class="{ 'bg-white': tab === '{{ $tabId }}' }"
                >
                    {{ __($tabLabel) }}
                </button>
            @endforeach
        </div>

        {{ $slot }}
    </div>
</x-filament::card>
