@props([
    'form',
])

<div
    {{ $attributes->class(['filament-tables-filters-form space-y-6']) }}
    x-data
>
    {{ $form }}

    <div class="{{ $this->shouldApplyTableFiltersByButton() ? 'flex justify-between' : 'text-end' }}">
        <x-tables::link
            wire:click="resetTableFiltersForm"
            color="danger"
            tag="button"
            size="sm"
        >
            {{ __('tables::table.filters.buttons.reset.label') }}
        </x-tables::link>
        @if($this->shouldApplyTableFiltersByButton())
            <x-tables::button
                wire:click="applyTableFiltersForm"
                color="primary"
                size="sm"
            >
                {{ __('tables::table.filters.buttons.apply.label') }}
            </x-tables::button>
        @endif
    </div>
</div>
