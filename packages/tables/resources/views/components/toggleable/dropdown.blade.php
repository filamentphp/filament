@props([
    'form',
    'maxHeight' => null,
    'triggerAction',
    'width' => null,
])

<x-filament::dropdown
    :max-height="$maxHeight"
    placement="bottom-end"
    shift
    :width="$width"
    wire:key="{{ $this->getId() }}.table.toggle"
    {{ $attributes->class(['fi-ta-col-toggling']) }}
>
    <x-slot name="trigger">
        {{ $triggerAction }}
    </x-slot>

    <div class="p-4">
        {{ $form }}
    </div>
</x-filament::dropdown>
