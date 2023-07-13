<x-filament-actions::action
    :action="$action"
    dynamic-component="filament::button"
    :outlined="$isOutlined()"
    :labeled-from="$getLabeledFromBreakpoint()"
    :icon-position="$getIconPosition()"
    :icon-size="$getIconSize()"
    class="fi-ac-btn-action"
>
    {{ $getLabel() }}
</x-filament-actions::action>
