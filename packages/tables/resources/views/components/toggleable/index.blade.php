@props([
    'form',
    'width' => 'sm',
])

<x-tables::dropdown {{ $attributes->class(['filament-tables-column-toggling']) }}>
    <x-slot name="trigger">
        <x-tables::toggleable.trigger />
    </x-slot>

    <div
        class="px-6 py-4 space-y-6"
        wire:ignore.self
        wire:key="{{ $this->id }}.table.toggle.form"
    >
        {{ $form }}
    </div>
</x-tables::dropdown>
