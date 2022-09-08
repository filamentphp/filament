@props([
    'form',
    'width' => null,
])

<x-tables::dropdown
    {{ $attributes->class(['filament-tables-column-toggling']) }}
    placement="bottom-end"
    shift
    :width="$width"
    wire:key="{{ $this->id }}.table.toggle"
>
    <x-slot name="trigger">
        <x-tables::toggleable.trigger />
    </x-slot>

    <div class="p-4">
        {{ $form }}
    </div>
</x-tables::dropdown>
