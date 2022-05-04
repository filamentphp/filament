<x-tables::actions.action
    :action="$action"
    component="tables::button"
    size="sm"
    :outlined="$isOutlined()"
    :icon-position="$getIconPosition()"
    class="filament-tables-button-action"
>
    {{ $getLabel }}
</x-tables::actions.action>
