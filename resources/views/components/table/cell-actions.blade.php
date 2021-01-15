@props([
    'label' => __('Edit'),
])

<x-filament::table.cell class="text-right">
    <x-filament::dropdown 
        class="transform text-gray-400 hover:text-gray-600 transition duration-200"
        x-bind:class="{ 'rotate-90': menuIsOpen }"
    >
        <x-slot name="button">
            <x-heroicon-o-dots-horizontal 
                class="w-4 h-4" 
                aria-hidden="true" 
            />
            <span class="sr-only">{{ $label }}</span>
        </x-slot>

        {{ $slot }}
    </x-filament::dropdown>
</x-filament::table.cell>