<x-tables::icon-button
    icon="heroicon-o-dots-vertical"
    x-on:click="$refs.panel.toggle"
    :label="__('tables::table.buttons.open_actions.label')"
    {{ $attributes->class(['filament-tables-bulk-actions-trigger']) }}
/>
