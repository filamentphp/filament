@props([
    'form',
    'width' => null,
])

<x-filament-tables::dropdown
    {{ $attributes->class(['filament-tables-column-toggling']) }}
    placement="bottom-end"
    shift
    :width="$width"
    wire:key="{{ $this->id }}.table.toggle"
>
    <x-slot name="trigger">
        <x-filament-tables::toggleable.trigger />
    </x-slot>

    <div class="p-4">
        {{ $form }}
    </div>
</x-filament-tables::dropdown>
