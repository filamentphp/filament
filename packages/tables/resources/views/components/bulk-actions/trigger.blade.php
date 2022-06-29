<x-tables::icon-button
    icon="heroicon-o-dots-vertical"
    x-on:click="$float($refs.panel, { placement: 'bottom-start', offset: 8, flip: {} })"
    :label="__('tables::table.buttons.open_actions.label')"
    {{ $attributes->class(['filament-tables-bulk-actions-trigger']) }}
/>
