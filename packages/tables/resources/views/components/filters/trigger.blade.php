<x-tables::icon-button
    icon="heroicon-o-filter"
    x-on:click="$refs.popoverPanel.toggle"
    :label="__('tables::table.buttons.filter.label')"
    :indicator="$indicator ?? null"
    {{ $attributes->class(['filament-tables-filters-trigger']) }}
/>
