<x-tables::icon-button
    icon="heroicon-o-view-boards"
    x-on:click="$float($refs.panel, { placement: 'bottom-end', offset: 8, flip: {}, shift: {} })"
    :label="__('tables::table.buttons.toggle_columns.label')"
    {{ $attributes->class(['filament-tables-column-toggling-trigger']) }}
/>
