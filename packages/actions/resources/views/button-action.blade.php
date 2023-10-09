<x-filament-actions::action
    :action="$action"
    :badge="$getBadge()"
    :badge-color="$getBadgeColor()"
    dynamic-component="filament::button"
    :icon-position="$getIconPosition()"
    :icon-size="$getIconSize()"
    :labeled-from="$getLabeledFromBreakpoint()"
    :outlined="$isOutlined()"
    :size="$getSize()"
    class="fi-ac-btn-action"
>
    {{ $getLabel() }}
</x-filament-actions::action>
