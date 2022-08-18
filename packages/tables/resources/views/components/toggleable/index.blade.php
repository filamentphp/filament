@props([
    'form',
    'width' => 'sm',
])

<x-tables::dropdown
    {{ $attributes->class(['filament-tables-column-toggling']) }}
    placement="bottom-end"
    wire:key="{{ $this->id }}.table.toggle"
>
    <x-slot name="trigger">
        <x-tables::toggleable.trigger />
    </x-slot>

    <div class="px-6 py-4 space-y-6">
        {{ $form }}
    </div>
</x-tables::dropdown>
