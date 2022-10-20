<x-filament-tables::actions.action
    :action="$action"
    component="filament-tables::button"
    :outlined="$isOutlined()"
    :icon-position="$getIconPosition()"
    class="filament-tables-button-action"
>
    {{ $getLabel() }}
</x-filament-tables::actions.action>
