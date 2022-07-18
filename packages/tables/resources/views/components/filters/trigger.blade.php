<x-tables::icon-button
    icon="heroicon-o-filter"
    x-on:click="$refs.panel.toggle"
    :label="__('tables::table.buttons.filter.label')"
    {{ $attributes->class(['filament-tables-filters-trigger']) }}
/>
