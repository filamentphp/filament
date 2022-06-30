<x-tables::icon-button
    icon="heroicon-o-dots-vertical"
    x-on:click="$float({ placement: 'bottom-start', offset: 8, flip: {} }, {trap: true})"
    :label="__('tables::table.buttons.open_actions.label')"
    {{ $attributes->class(['filament-tables-bulk-actions-trigger']) }}
/>
