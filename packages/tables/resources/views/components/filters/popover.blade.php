@props([
    'form',
    'width' => 'xs',
    'indicatorsCount' => null,
])

@php
    if ($this->shouldApplyTableFiltersByButton()) {
        $attributes->setAttributes(['x-init' => '$watch(\'panelIsShown\', state => { if (state === false) $wire.refreshTableFiltersForm() })']);
    }
@endphp

<x-tables::dropdown
    {{ $attributes->class(['filament-tables-filters']) }}
    placement="bottom-end"
    shift
    :width="$width"
    wire:key="{{ $this->id }}.table.filters"
>
    <x-slot name="trigger">
        <x-tables::filters.trigger :indicators-count="$indicatorsCount" />
    </x-slot>

    <x-tables::filters
        class="p-4"
        :form="$form"
    />
</x-tables::dropdown>
