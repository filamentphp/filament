<x-tables::icon-button
    icon="heroicon-o-filter"
    x-on:click="$float({ placement: 'bottom-end', offset: 8, flip: {} }, {trap: true})"
    :label="__('tables::table.buttons.filter.label')"
    {{ $attributes->class(['filament-tables-filters-trigger']) }}
/>
