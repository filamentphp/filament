@props([
    'form'
])

<div class="space-y-6 filament-tables-filters-form" x-data="{}">
    {{ $form }}

    <div class="text-right">
        <x-tables::link
            wire:click="resetTableFiltersForm"
            color="danger"
            tag="button"
            size="sm"
        >
            {{ __('tables::table.filters.buttons.reset.label') }}
        </x-tables::link>
    </div>
</div>
