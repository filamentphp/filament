<x-filament-actions::action
    :action="$action"
    dynamic-component="filament::button"
    :outlined="$isOutlined()"
    :labeled-from="$getLabeledFromBreakpoint()"
    :icon-position="$getIconPosition()"
    :icon-size="$getIconSize()"
    class="filament-actions-button-action"
>
    {{ $getLabel() }}
</x-filament-actions::action>
