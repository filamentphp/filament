<x-tables::icon-button
    icon="heroicon-o-filter"
    x-on:click="isOpen = ! isOpen"
    :label="__('tables::table.buttons.filter.label')"
    {{ $attributes->class(['filament-tables-filters-trigger']) }}
/>
