<x-filament-actions::group
    :badge="$getBadge()"
    :badge-color="$getBadgeColor()"
    dynamic-component="filament::button"
    :group="$group"
    :icon-position="$getIconPosition()"
    :labeled-from="$getLabeledFromBreakpoint()"
    :outlined="$isOutlined()"
    :size="$getSize()"
    class="fi-ac-btn-group"
>
    {{ $getLabel() }}
</x-filament-actions::group>
