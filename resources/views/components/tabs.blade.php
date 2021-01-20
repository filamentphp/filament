@props([
    'label',
    'tab',
])

<x-filament::card
    x-data="{ tab: '{{ $tab }}' }"
    x-cloak
    class="overflow-hidden"
>
    <div class="-m-4 md:-m-6">
        <div class="bg-gray-100 border-b border-gray-200 flex" class="tablist" role="tablist" aria-label="{{ $label }}">
            {{ $tablist }}
        </div>

        {{ $slot }}
    </div>
</x-filament::card>
