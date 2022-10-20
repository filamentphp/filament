@props([
    'form',
])

<div
    {{ $attributes->class(['filament-tables-filters-form space-y-6']) }}
    x-data
>
    {{ $form }}

    <div class="text-right">
        <x-filament-support::link
            wire:click="resetTableFiltersForm"
            color="danger"
            tag="button"
            size="sm"
        >
            {{ __('filament-tables::table.filters.buttons.reset.label') }}
        </x-filament-support::link>
    </div>
</div>
