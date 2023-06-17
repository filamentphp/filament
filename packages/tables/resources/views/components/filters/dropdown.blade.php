@props([
    'form',
    'indicatorsCount' => null,
    'maxHeight' => null,
    'triggerAction',
    'width' => 'xs',
])

<x-filament::dropdown
    {{ $attributes->class(['filament-tables-filters']) }}
    :max-height="$maxHeight"
    placement="bottom-end"
    shift
    :width="$width"
    wire:key="{{ $this->id }}.table.filters"
>
    <x-slot name="trigger" class="flex items-center justify-center">
        {{ $triggerAction->indicator($indicatorsCount) }}
    </x-slot>

    <x-filament-tables::filters class="p-4" :form="$form" />
</x-filament::dropdown>
