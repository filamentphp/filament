@props([
    'form',
    'maxHeight' => null,
    'width' => 'xs',
    'indicatorsCount' => null,
])

<x-tables::dropdown
    {{ $attributes->class(['filament-tables-filters']) }}
    :max-height="$maxHeight"
    placement="bottom-end"
    shift
    :width="$width"
    wire:key="{{ $this->id }}.table.filters"
>
    <x-slot name="trigger">
        <x-tables::filters.trigger :indicators-count="$indicatorsCount" />
    </x-slot>

    <x-tables::filters class="p-4" :form="$form" />
</x-tables::dropdown>
