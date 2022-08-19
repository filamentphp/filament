<x-tables::icon-button
    icon="heroicon-s-filter"
    color="secondary"
    x-on:click="$refs.popoverPanel.toggle"
    :label="__('tables::table.buttons.filter.label')"
    {{ $attributes->class(['filament-tables-filters-trigger']) }}
/>
