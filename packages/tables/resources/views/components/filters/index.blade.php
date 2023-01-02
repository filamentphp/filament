@props([
    'form',
])

<div {{ $attributes->class(['filament-tables-filters-form space-y-6']) }}>
    {{ $form }}

    <div class="flex items-center justify-between gap-3">
        <div>
            <x-filament::loading-indicator
                wire:target="$set,tableFilters,resetTableFiltersForm"
                wire:loading.delay=""
                class="h-4 w-4 text-gray-700"
            />
        </div>

        <x-filament::link
            wire:click="resetTableFiltersForm"
            color="danger"
            tag="button"
            size="sm"
        >
            {{ __('filament-tables::table.filters.buttons.reset.label') }}
        </x-filament::link>
    </div>
</div>
