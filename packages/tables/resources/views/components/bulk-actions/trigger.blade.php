<x-filament::icon-button
    icon="heroicon-o-ellipsis-vertical"
    icon-alias="tables::bulk-actions.trigger"
    :label="__('filament-tables::table.buttons.open_bulk_actions.label')"
    class="sm:hidden"
    {{ $attributes->class(['filament-tables-bulk-actions-trigger']) }}
/>

<x-filament::button
    icon="heroicon-m-ellipsis-vertical"
    icon-alias="tables::bulk-actions.trigger"
    color="gray"
    class="hidden sm:inline-flex"
    {{ $attributes->class(['filament-tables-bulk-actions-trigger']) }}
>
    {{ __('filament-tables::table.buttons.open_bulk_actions.label') }}
</x-filament::button>
