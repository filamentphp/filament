<x-tables::icon-button
    icon="heroicon-o-view-boards"
    x-on:click="$refs.toggleablePanel.toggle"
    :label="__('tables::table.buttons.toggle_columns.label')"
    {{ $attributes->class(['filament-tables-column-toggling-trigger']) }}
/>
