<x-filament-actions::group
    dynamic-component="filament::button"
    :group="$group"
    :icon-position="$getIconPosition()"
    :icon-size="$getIconSize()"
    :labeled-from="$getLabeledFromBreakpoint()"
    :outlined="$isOutlined()"
    :size="$getSize()"
    class="fi-ac-btn-group"
>
    {{ $getLabel() }}
</x-filament-actions::group>
