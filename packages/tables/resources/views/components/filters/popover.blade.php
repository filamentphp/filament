@props([
    'form',
    'width' => 'sm',
])

<x-tables::dropdown
    {{ $attributes->class(['filament-tables-filters']) }}
    placement="bottom-end"
    width="xs"
    wire:key="{{ $this->id }}.table.filters"
>
    <x-slot name="trigger">
        <x-tables::filters.trigger />
    </x-slot>

    <x-tables::filters
        class="p-4"
        :form="$form"
    />
</x-tables::dropdown>
