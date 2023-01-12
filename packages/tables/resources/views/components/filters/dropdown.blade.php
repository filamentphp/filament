@props([
    'form',
    'width' => 'xs',
    'indicatorsCount' => null,
])

<x-filament::dropdown
    {{ $attributes->class(['filament-tables-filters']) }}
    placement="bottom-start"
    shift
    :width="$width"
    wire:key="{{ $this->id }}.table.filters"
>
    <x-slot name="trigger">
        <x-filament-tables::filters.trigger :indicators-count="$indicatorsCount" />
    </x-slot>

    <x-filament-tables::filters
        class="p-4"
        :form="$form"
    />
</x-filament::dropdown>
