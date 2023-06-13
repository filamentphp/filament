@props([
    'form',
    'maxHeight' => null,
    'triggerAction',
    'width' => null,
])

<x-filament::dropdown
    {{ $attributes->class(['filament-tables-column-toggling']) }}
    :max-height="$maxHeight"
    placement="bottom-start"
    shift
    :width="$width"
    wire:key="{{ $this->getId() }}.table.toggle"
>
    <x-slot name="trigger">
        {{ $triggerAction }}
    </x-slot>

    <div class="p-4">
        {{ $form }}
    </div>
</x-filament::dropdown>
