@props([
    'form',
    'width' => 'sm',
])

<x-tables::dropdown
    {{ $attributes->class(['filament-tables-filters']) }}
    placement="bottom-end"
    wire:key="{{ $this->id }}.table.filters"
>
    <x-slot name="trigger">
        <x-tables::filters.trigger />
    </x-slot>

    <div class="px-6 py-4 space-y-6">
        <x-tables::icon-button
            icon="heroicon-o-x"
            x-on:click="close"
            :label=" __('tables::table.filters.buttons.close.label')"
            color="secondary"
            class="absolute top-3 right-3 rtl:right-auto rtl:left-3"
        />

        <x-tables::filters :form="$form" />
    </div>
</x-tables::dropdown>
