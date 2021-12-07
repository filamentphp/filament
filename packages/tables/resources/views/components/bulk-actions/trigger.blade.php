<x-tables::icon-button
    icon="heroicon-o-dots-vertical"
    x-on:click="isOpen = ! isOpen"
    :label="__('tables::table.buttons.open_actions.label')"
    {{ $attributes }}
/>
