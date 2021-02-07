@props([
    'defaultTabId',
    'label',
])

<x-filament::card
    x-data="{ tab: '{{ $defaultTabId }}' }"
    x-cloak
    class="overflow-hidden"
>
    <div class="-m-4 md:-m-6">
        <div aria-label="{{ $label }}" role="tablist" class="bg-gray-100 border-b border-gray-200 flex">
            {{ $tabList }}
        </div>

        {{ $slot }}
    </div>
</x-filament::card>
