@props([
    'form',
    'width' => 'sm',
])

<x-tables::dropdown
    {{ $attributes->class(['filament-tables-column-toggling']) }}
    placement="bottom-end"
    shift
    wire:key="{{ $this->id }}.table.toggle"
>
    <x-slot name="trigger">
        <x-tables::toggleable.trigger />
    </x-slot>

    <div class="p-4">
        {{ $form }}
    </div>
</x-tables::dropdown>
