@props([
    'form',
    'width' => 'sm',
])

<x-tables::dropdown {{ $attributes->class(['filament-tables-filters']) }}>
    <x-slot name="trigger">
        <x-tables::filters.trigger />
    </x-slot>

    <div
        class="px-6 py-4 space-y-6"
        wire:ignore.self
        wire:key="{{ $this->id }}.table.filters"
    >
        <x-tables::icon-button
            icon="heroicon-o-x"
            x-on:click="$refs.popoverPanel.close"
            :label=" __('tables::table.filters.buttons.close.label')"
            color="secondary"
            class="absolute top-3 right-3 rtl:right-auto rtl:left-3"
        />

        <x-tables::filters :form="$form" />
    </div>
</x-tables::dropdown>
