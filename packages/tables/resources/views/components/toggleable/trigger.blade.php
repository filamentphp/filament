<x-filament::icon-button
    icon="heroicon-m-view-columns"
    icon-alias="tables::column-toggling.trigger"
    color="gray"
    :label="__('filament-tables::table.buttons.toggle_columns.label')"
    {{ $attributes->class(['filament-tables-column-toggling-trigger']) }}
/>
